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

/**
 * Class SpnFactory
 *
 * A very, very simple class for getting `SPN` or `DGM` instances.
 *
 * @package cobisja\Spn
 */
class SpnFactory
{
    /**
     * Type `SPN`
     */
    const SIMPLE_PUSH_NOTIFIER = 0;

    /**
     * Type `DGM`
     */
    const DEVICE_GROUP_MANAGER = 1;


    /**
     * Get the object instance requested.
     *
     * @param int $type Object Id.
     *
     * @return DeviceGroupManager|Spn
     */
    public function make($type)
    {
        if (self::SIMPLE_PUSH_NOTIFIER == $type) {
            return new Spn();
        } else {
            return new DeviceGroupManager();
        }
    }
}