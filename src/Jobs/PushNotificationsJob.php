<?php

namespace cobisja\Spn\Jobs;

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

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Log;

/**
 * Class PushNotificationsJob
 *
 * Due SPN class perform all `push notifications` tasks using FCM HTTP protocol, all
 * requests performed ara synchronous, so it is a very common scenario that your app
 * needs to perform all these `push notification ` in a `asynchronous` way. This can
 * be achieved using Laravel's Jobs and Queues, so this class is a very simple Job
 * to handle all the `push notifications` performed by youd app.
 *
 * @package cobisja\Spn\Jobs
 */
class PushNotificationsJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Device tokens array to send the Notification.
     *
     * @var array
     */
    protected $recipients;

    /**
     * Notification's title.
     *
     * @var string
     */
    protected $title;

    /**
     * Notification's body.
     *
     * @var string
     */
    protected $body;

    /**
     * Create a new job instance.
     *
     * @param array $recipients
     * @param string $title
     * @param string $body
     */
    public function __construct(array $recipients, string $title, string $body)
    {
        $this->recipients = $recipients;
        $this->title = $title;
        $this->body = $body;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        // Now, let's send the notification via FCM
        $result = push_notification($this->recipients, $this->title, $this->body);

        // Logging for debugging purposes.
        Log::info($result);

        return $result;
    }
}