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

if ( ! function_exists('push_notification')) {

    /**
     * Helper for `pushing notifications`
     *
     * @param string|array $recipients Recipients list.
     * @param string $title Notification's title.
     * @param string $body Notification's title.
     * @param array $options Notifications's options. (@see https://firebase.google.com/docs/cloud-messaging/http-server-ref#table1)
     *
     * @return mixed
     */
    function push_notification($recipients, string $title, string $body, array $options = [] )
    {
        return app('spn')
            ->make(\cobisja\Spn\SpnFactory::SIMPLE_PUSH_NOTIFIER)
            ->pushNotificationTo($recipients, $title, $body, $options);
    }
}

if ( ! function_exists('dgm_create')) {

    /**
     * Helper for creating a `device group`
     *
     * @see https://firebase.google.com/docs/cloud-messaging/android/device-group
     *
     * @param array $registrationIds Devices registration tokens list
     * @param string $notificationKeyName Name for the group.
     *
     * @return mixed
     */
    function dgm_create(array $registrationIds, string $notificationKeyName)
    {
        return app('spn')
            ->make(\cobisja\Spn\SpnFactory::DEVICE_GROUP_MANAGER)
            ->create($registrationIds, $notificationKeyName);
    }
}

if ( ! function_exists('dgm_add')) {
    /**
     * Helper for adding members to a `device group`.
     *
     * @see https://firebase.google.com/docs/cloud-messaging/android/device-group
     *
     * @param array $registrationIds Devices registration tokens list to be added.
     * @param string $notificationKey Group's token.
     * @param string $notificationKeyName Name for the group.
     *
     * @return mixed
     */
    function dgm_add(array $registrationIds, string $notificationKey, string $notificationKeyName = null)
    {
        return app('spn')
            ->make(\cobisja\Spn\SpnFactory::DEVICE_GROUP_MANAGER)
            ->add($registrationIds, $notificationKey, $notificationKeyName);
    }
}

if ( ! function_exists('dgm_remove')) {
    /**
     * Helper for removing members to a `device group`.
     *
     * @see https://firebase.google.com/docs/cloud-messaging/android/device-group
     *
     * @param array $registrationIds Devices registration tokens list to be removed.
     * @param string $notificationKey Group's token.
     * @param string $notificationKeyName Name for the group.
     *
     * @return mixed
     */
    function dgm_remove(array $registrationIds, string $notificationKey, string $notificationKeyName = null)
    {
        return app('spn')
            ->make(\cobisja\Spn\SpnFactory::DEVICE_GROUP_MANAGER)
            ->remove($registrationIds, $notificationKey, $notificationKeyName);
    }
}