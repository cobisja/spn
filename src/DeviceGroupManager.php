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

use cobisja\Spn\Exceptions\Dgm\NotificationKeyAlreadyExistsException;
use cobisja\Spn\Exceptions\Dgm\NotificationKeyNotFoundException;
use Illuminate\Http\Response;

class DeviceGroupManager
{
    /**
     * DGM `Create` action string.
     */
    const CREATE_ACTION = 'create';

    /**
     * DGM `Add` action string.
     */
    const ADD_ACTION = 'add';

    /**
     * DGM `Remove` action string.
     */
    const REMOVE_ACTION = 'remove';

    /**
     * FCM response key for `Notification_Key Already Exists` Error.
     */
    const NOTIFICATION_KEY_ALREADY_EXISTS_ERROR = 'notification_key already exists';

    /**
     * FCM response key for `Notification_Key Not Found` Error.
     */
    const NOTIFICATION_KEY_NOT_FOUND_ERROR = 'notification_key not found';

    /**
     * FCM Response Error key.
     */
    const ERROR_KEY = 'error';

    /**
     * Create a Device Group
     *
     * @param array $registrationIds Registration Tokens
     * @param string $notificationKeyName Unique name for given group.
     *
     * @return Response
     */
    public function create(array $registrationIds, string $notificationKeyName) : Response
    {
        return $this->perform(self::CREATE_ACTION, $registrationIds, null, $notificationKeyName);
    }

    /**
     * Add devices to an existing group.
     *
     * @param array $registrationIds Devices tokens to be added.
     * @param string $notificationKey Key representing the group.
     * @param null $notificationKeyName Name for given group.
     *
     * @return Response
     */
    public function add(array $registrationIds, string $notificationKey, $notificationKeyName = null) : Response
    {
        return $this->perform(self::ADD_ACTION, $registrationIds, $notificationKey, $notificationKeyName);
    }

    /**
     * Remove devices from an existing group.
     *
     * @param array $registrationIds Devices tokens to be removed.
     * @param string $notificationKey Key representing the group.
     * @param null $notificationKeyName Name for given group.
     *
     * @return Response
     */
    public function remove(array $registrationIds, string $notificationKey, $notificationKeyName = null) : Response
    {
        return $this->perform(self::REMOVE_ACTION, $registrationIds, $notificationKey, $notificationKeyName);
    }

    /**
     * Perform the requested action on the group.
     *
     * @param string $action Action to be performed (`create`, `add`, `remove`)
     * @param array $registrationIds Devices tokens array
     * @param $notificationKey Notification Key for the group
     * @param $notificationKeyName Name for the group.
     *
     * @return Response
     */
    private function perform (
        string $action,
        array $registrationIds,
        $notificationKey,
        $notificationKeyName
    ) : Response
    {
        // Getting the Device Group Manager endpoint
        $endpoint = config('spn.dgm_endpoint');

        // Headers building.
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'key=' . config('spn.fcm_key'),
            'project_id' => config('spn.fcm_project_id')
        ];

        // Request payload building.
        $data = array_filter(
            [
            'operation' => $action,
            'notification_key' => $notificationKey,
            'notification_key_name' => $notificationKeyName
            ],
            'strlen'
        );

        // Adding the Devices tokens array
        $data['registration_ids'] = $registrationIds;

        // Getting a `Requester` instance for making the request.
        $requester = new Requester($endpoint, $headers, $data);

        // Request sending.
        $response = $requester->send();

        // Response extracting.
        $body = json_decode($response->getBody(), true);

        // Any error reported by FCM?
        isset($responseBody[self::ERROR_KEY]) && $this->triggerException($body);

        // Response returning.
        return response($body, Response::HTTP_OK)->withHeaders($response->getHeaders());
    }

    /**
     * Throw an Exception according the FCM's Error response.
     *
     * @param $responseBody
     *
     * @throws NotificationKeyAlreadyExistsException
     * @throws NotificationKeyNotFoundException
     */
    private function triggerException($responseBody)
    {
        if (self::NOTIFICATION_KEY_ALREADY_EXISTS_ERROR === $responseBody[self::ERROR_KEY]) {
            throw new NotificationKeyAlreadyExistsException();
        } elseif (self::NOTIFICATION_KEY_NOT_FOUND_ERROR === $responseBody[self::ERROR_KEY]) {
            throw new NotificationKeyNotFoundException();
        }
    }
}