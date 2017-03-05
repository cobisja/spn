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

use Exception;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Response as HttpResponse;

/**
 * Class SpnException
 *
 * @see https://firebase.google.com/docs/cloud-messaging/http-server-ref#table4
 * @see https://firebase.google.com/docs/cloud-messaging/http-server-ref#table9
 *
 * @package cobisja\Spn\Exceptions
 */
class SpnException extends Exception
{
    /**
     * Default Fatal Error Message.
     */
    const FATAL_ERROR_MESSAGE = 'Fatal Error';

    /**
     * Spn response.
     *
     * @var Response
     */
    private $response;

    /**
     * SpnException constructor.
     *
     * @param Response $response Response returned from FCM.
     * @param string|null $altMessage Optional Error message.
     * @param Exception|null $previous
     */
    public function __construct(Response $response, string $altMessage = null, Exception $previous = null)
    {
        // Error Response saving.
        $this->response = $response;

        // Calling Exception parent constructor
        parent::__construct($altMessage ?? $this->getErrorString(), $this->getErrorCode(), $previous);
    }

    /**
     * Get Response Error Code.
     *
     * @return integer
     */
    public function getErrorCode() : int
    {
        return $this->response->getStatusCode() ?? HttpResponse::HTTP_INTERNAL_SERVER_ERROR;
    }

    /**
     * Return Response Error Data as String.
     *
     * @return string
     */
    public function getErrorString() : string
    {
        return $this->response->getReasonPhrase() ?? self::FATAL_ERROR_MESSAGE;
    }
}