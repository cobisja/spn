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

use cobisja\Spn\Exceptions\ConnectionException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Requester
 *
 * Class for exposing a `Guzzle client` to be used as `request carrier` for all
 * requests sent to Firebase Cloud Messaging / Device Group Messaging endpoint.
 *
 * @package cobisja\Spn
 */
class Requester
{
    /**
     * FCM/DGM endpoint.
     *
     * @var string
     */
    private $endpoint;

    /**
     * Request's headers.
     *
     * @var array
     */
    private $headers;

    /**
     * Request's data.
     *
     * @var array
     */
    private $data;

    /**
     * Requester constructor.
     *
     * @param string $endpoint
     * @param array $headers
     * @param array $data
     */
    public function __construct(string $endpoint, array $headers, array $data)
    {
        $this->endpoint = $endpoint;
        $this->headers = $headers;
        $this->data = $data;
    }

    /**
     * Perform the request.
     *
     * @return ResponseInterface
     *
     * @throws ConnectionException
     */
    public function send() : ResponseInterface
    {
        // Getting a `GuzzleClient` instance.
        $client =  new Client();

        try {
            $response = $client->post(
                $this->endpoint,
                ['http_errors' => false, 'headers' => $this->headers, 'json' => $this->data]
            );
        } catch (ConnectException $e) {
            throw new ConnectionException('Cannot connect to FCM endpoint');
        }

        // Response returning.
        return $response;
    }
}