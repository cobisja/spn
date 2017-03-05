<?php
/*
 * SPN - Simple Push Notifications via Firebase Cloud Messaging
 *
 * (The MIT License)
 *
 * Copyright (c) 2017 Bake250 [http://www.bake250.com]
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

return [
    /**
     * Your Project ID in FCM.
     */
    'fcm_project_id' => env('FCM_PROJECT_ID'),

    /**
     * Your FCM Key.
     */
    'fcm_key' => env('FCM_KEY'),

    /**
     * FCM endpoint.
     */
    'fcm_endpoint' => env('FCM_ENDPOINT'),

    /**
     * Device Group Manager endpoing.
     */
    'dgm_endpoint' => env('DGM_ENDPOINT'),

    /**
     * Run the Exceptions triggering behaviour.
     */
    'exceptions_triggering' => [

        /**
         * Trigger exceptions for any error condition.
         */
        'on_error' => true,

        /**
         * Trigger exception for `Partial Notifications Deliveries`
         */
        'on_partial_deliveries' => false
    ]
];