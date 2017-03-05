<?php
/*
 * SPN - Simple Push Notifications via Firebase Cloud Messaging
 *
 * (The MIT License)
 *
 * Copyright (c) 2017 - Cobis [jcobis@gmail.com]
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace cobisja\Spn;

use cobisja\Spn\Exceptions\BadRequestException;
use cobisja\Spn\Exceptions\InvalidServerKeyException;
use cobisja\Spn\Exceptions\UndeliveredNotificationException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Response as HttpResponse;

/**
 * Class Spn
 *
 * SPN - Simple Push Notifications.
 *
 * Class for Push Notifications using Firebase Cloud Messaging Protocol.
 *
 * Such as its name suggests, the class is intended for providing a simple way for
 * pushing notifications, nothing else. It has not support for pushing `messages` or
 * notifications based on `topics`
 *
 * @see https://firebase.google.com/docs/cloud-messaging/server
 *
 * Main features about SPN are:
 *
 * - Intended for only push notifications.
 * - Push notifications are sent using only HTTP Protocol.
 * - It is able for returning the FCM raw response or a parsed one.
 * - Parsed responses have information about delivered / undelivered notifications,
 *   devices tokens that should be deleted, those ones should be resend.
 * - Also It has support for `Group Device Messaging`
 *      @see https://firebase.google.com/docs/cloud-messaging/android/device-group
 *      @see https://firebase.google.com/docs/cloud-messaging/ios/device-group
 *
 * For all about errors and exception triggering, please take a look at:
 * @see https://firebase.google.com/docs/cloud-messaging/http-server-ref#table4
 * @see https://firebase.google.com/docs/cloud-messaging/http-server-ref#table9
 *
 *
 * @package cobisja\Spn
 */
class Spn
{
    /**
     * Key used in `Sending Stats`, representing devices tokens
     * that have received the notification.
     */
    const DELIVERED = 'delivered';

    /**
     * Key used in `Sending Stats`, representing devices tokens
     * that have not received the notification.
     */
    const UNDELIVERED = 'undelivered';

    /**
     * Key used in `Sending Stats`, representing devices tokens
     * that are `invalid`, so they should be deleted.
     */
    const SHOULD_BE_DELETED = 'shouldBeDeleted';

    /**
     * Key used in `Sending Stats`, representing devices tokens
     * that were `unavailable` at the time of `pushing` the notification, so a `resend` should be performed.
     */
    const SHOULD_BE_RESEND = 'shouldBeResend';

    /**
     * `Error` key in FCM response.
     */
    const ERROR_KEY = 'error';

    /**
     * `No registered` key in FCM response.
     */
    const NOT_REGISTERED_KEY = 'NotRegistered';

    /**
     * `Invalid Registration` key in FCM response.
     */
    const INVALID_REGISTRATION_KEY = 'InvalidRegistration';

    /**
     * `Unavailable key` in FCM response.
     */
    const UNAVAILABLE_KEY = 'Unavailable';

    /**
     * `Device Message Rate Exceeded` key in FCM Response.
     */
    const DEVICE_MESSAGE_RATE_EXCEEDED_KEY = 'DeviceMessageRateExceeded';

    /**
     * `Results` Key in FCM Response.
     */
    const RESULTS_KEY = 'results';

    /**
     * `Failed Registration Ids` key in DGM response.
     */
    const FAILED_REGISTRATION_IDS_KEY = 'failed_registration_ids';

    /**
     * Push notification recipients.
     *
     * @var
     */
    private $recipients;

    /**
     * Holds `Push notification` stats.
     *
     * @var array
     */
    private $stats;

    /**
     * Holds the FCM response.
     *
     * @var mixed
     */
    private $response;

    /**
     * Spn constructor.
     */
    public function __construct()
    {
        $this->stats = [
            self::DELIVERED => [],
            self::UNDELIVERED => [],
            self::SHOULD_BE_DELETED => [],
            self::SHOULD_BE_RESEND => []
        ];
    }

    /**
     * Send the Push Notification via FCM.
     *
     * @param array $recipients Recipients (tokens) of the message
     * @param string $title Title of the message
     * @param string $body Body of the Message.
     * @param array $options
     *
     * @return bool|mixed
     */
    public function pushNotificationTo($recipients, string $title, string $body = '', array $options = [])
    {
        // Recipients storing.
        $this->recipients = $recipients;

        // Body building.
        $fields = array(
            'notification' => array_merge(['title' => $title, 'body' => $body, 'sound' => 'default'], $options),
            (is_array($recipients) ? 'registration_ids' : 'to') => $recipients,
        );

        // Headers building.
        $headers = [
            'Authorization' => 'key=' . config('spn.fcm_key'),
            'Content-Type' => 'application/json'
        ];

        // Requesting `Push Notification` delivering.
        $this->response = $this->push(config('spn.fcm_endpoint'), $headers, $fields);

        // For `method chaining` purposes.
        return $this;
    }

    /**
     * Return the FCM response.
     *
     * @return mixed
     */
    public function rawResponse()
    {
        return $this->response;
    }

    /**
     * Return the `Push notification` stats.
     *
     * @return array
     */
    public function stats() : array
    {
        return $this->stats;
    }

    /**
     * Return the Devices tokens that have received the notification.
     *
     * @return array
     */
    public function delivered() : array
    {
        return $this->stats[self::DELIVERED];
    }

    /**
     * Return the Devices tokens that have not received the notification.
     *
     * @return array
     */
    public function undelivered() : array
    {
        return $this->stats[self::UNDELIVERED];
    }

    /**
     * Return the Devices tokens that have been marked as `invalid` by FCM,
     * so they should be deleted from your database.
     *
     * @return null|array
     */
    public function tokensForDeletion()
    {
        return $this->stats[self::SHOULD_BE_DELETED];
    }

    /**
     * Return the Devices tokens that have been marked as `Unavailable` by FCM
     * at the time of performing the `Push notification` task, so they are
     * susceptible for a `resending` operation.
     *
     * @return null|array
     */
    public function tokensForResending()
    {
        return $this->stats[self::SHOULD_BE_RESEND];
    }

    /**
     * Build a `Response` object.
     *
     * @param Response $response Response from RCM
     *
     * @return HttpResponse
     */
    protected function buildResponse(Response $response) : HttpResponse
    {
        return response(json_decode($response->getBody(), true), HttpResponse::HTTP_OK)
            ->withHeaders($response->getHeaders());
    }

    /**
     * Build a `Push notification` stats after perform the `pushing` operation.
     * It holds:
     *
     * + Devices token list that have received the notification.
     * + Devices token list that have not received the notification.
     * + Devices token list marked as `Invalid`.
     * + Devices token list marked as `Susceptible for Resending`.
     *
     * @param Response $response
     */
    protected function buildStats(Response $response)
    {
        // Turning into array the recipient list.
        $recipients = array_flatten([$this->recipients]);

        // Parse the FCM Response as an PHP array.
        $body = json_decode($response->getBody(), true);

        // Push notification through Devices Group Messaging?
        if (!isset($body[self::RESULTS_KEY])) {
            // Let's get the `undeliverd` devices token list.
            $this->stats[self::UNDELIVERED] = isset($body[self::FAILED_REGISTRATION_IDS_KEY]) ?
                $body[self::FAILED_REGISTRATION_IDS_KEY] : [];

            // DGM does not offer information about tokens to be `deleted` or `resend`
            $this->stats[self::SHOULD_BE_DELETED] = null;
            $this->stats[self::SHOULD_BE_RESEND] = null;
        } else {
            // It is a FCM response.
            foreach ($body[self::RESULTS_KEY] as $index => $value) {
                // Have an error?
                if (isset($value[self::ERROR_KEY])) {
                    // Let's get the `undelivered` token list and...
                    array_push($this->stats[self::UNDELIVERED], $recipients[$index]);

                    // ... the `should be deleted` devices token list and ...
                    if (self::NOT_REGISTERED_KEY === $value[self::ERROR_KEY] || self::INVALID_REGISTRATION_KEY === $value[self::ERROR_KEY]) {
                        array_push($this->stats[self::SHOULD_BE_DELETED], $recipients[$index]);
                    }

                    // ... the `should be resend` devices token list.
                    if (self::UNAVAILABLE_KEY === $value[self::ERROR_KEY] || self::DEVICE_MESSAGE_RATE_EXCEEDED_KEY === $value[self::ERROR_KEY]) {
                        array_push($this->stats[self::SHOULD_BE_RESEND], $recipients[$index]);
                    }
                }
            }
        }

        // Finally, let's build the `delivered` devices token list.
        $this->stats[self::DELIVERED] = array_values(array_diff($recipients, $this->stats[self::UNDELIVERED]));
    }

    /**
     * Perform the `Push` operation via FCm
     *
     * @param string $endpoint FCM/DGM endpoint.
     * @param array $headers Request's header.
     * @param array $fields Request's fields.
     *
     * @return HttpResponse
     */
    private function push(string $endpoint, array $headers, array $fields)
    {
        // Gen a `Requester` instance for sending.
        $client = new Requester($endpoint, $headers, $fields);

        // Let's send the request to FCM/DGM endpoint.
        $response = $client->send();

        // Let's build the `Push notification` stats.
        $this->buildStats($response);

        // Checking for any Exception to be triggered.
        $this->checkForTriggerException($response);

        // Building and returning the response.
        return $this->buildResponse($response);
    }

    /**
     * Check if an Exception should be triggered based on configuration data and Response got.
     *
     * @param Response $response Response got from FCM/DGM
     *
     * @throws BadRequestException
     * @throws InvalidServerKeyException
     * @throws UndeliveredNotificationException
     */
    private function checkForTriggerException(Response $response)
    {
        // Extracting triggering exception config.
        $triggerOnError = config('spn.exceptions_triggering.on_error', true);
        $triggerOnPartialDeliveries = config('spn.exceptions_triggering.on_partial_deliveries', false);

        // Parsed response as PHP array.
        $body = json_decode($response->getBody(), true);

        // Now, let's check the triggering exception conditions.
        switch ($response->getStatusCode()) {
            case HttpResponse::HTTP_UNAUTHORIZED:
                if ($triggerOnError) {
                    throw new InvalidServerKeyException($response);
                }
                break;

            case HttpResponse::HTTP_BAD_REQUEST:
                if ($triggerOnError) {
                    throw new BadRequestException($response, 'Possible malformed JSON payload');
                }
                break;

            case HttpResponse::HTTP_OK:
                if ($triggerOnPartialDeliveries && 0 < $body['failure']) {
                    throw new UndeliveredNotificationException($this, $response, (0 === $body['success'] ?
                        'None of the notifications could be delivered' :
                        'Some of the notifications could not be delivered')
                    );
                }
                break;
            default:
                if ($triggerOnError) {
                    throw new InvalidServerKeyException($response);
                }
        }
    }
}