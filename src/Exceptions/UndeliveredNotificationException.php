<?php
/*
 * SPN - Simple Push Notifications via Firebase Cloud Messaging
 *
 * (The MIT License)
 *
 * Copyright (c) 2017 cobisja [http://www.cobisja.com]
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

namespace cobisja\Spn\Exceptions;

use cobisja\Spn\Spn;
use Exception;
use GuzzleHttp\Psr7\Response;

/**
 * Class UndeliveredNotificationException
 *
 * @see https://firebase.google.com/docs/cloud-messaging/http-server-ref#table4
 * @see https://firebase.google.com/docs/cloud-messaging/http-server-ref#table9
 *
 * @package cobisja\Spn\Exceptions
 */
class UndeliveredNotificationException extends SpnException
{
    /**
     * @var Spn
     */
    private $spn;

    /**
     * UndeliveredNotificationException constructor.
     *
     * @param Spn $spn Holds an Spn instance.
     * @param Response $response Response returned from FCM.
     * @param null $altMessage Optional Error message.
     * @param Exception|null $previous
     */
    public function __construct(Spn $spn, Response $response, $altMessage = null, Exception $previous = null)
    {
        // Saving Spn instance.
        $this->spn = $spn;

        // Calling parent's constructor.
        parent::__construct($response, $altMessage, $previous);
    }

    /**
     * Return an array of all Tokens ids that have received the notification.
     *
     * @return array
     */
    public function delivered() : array
    {
        return $this->spn->delivered();
    }

    /**
     * Return an array of all Tokens ids that have not received the notification.
     *
     * @return array
     */
    public function undelivered() : array
    {
        return $this->spn->undelivered();
    }

    /**
     * Return an array of all invalid Tokens, detected by FCM, that should be delete from the app.
     *
     * @return array|null
     */
    public function tokensForDeletion()
    {
        return $this->spn->tokensForDeletion();
    }

    /**
     * Return an array of all valid, but unavailable, tokens, that a `resend` task should be performed.
     *
     * @return array|null
     */
    public function tokensForResending()
    {
        return $this->spn->tokensForResending();
    }
}