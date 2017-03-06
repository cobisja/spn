# SPN - Simple Push Notification

A very simple way for sending, and nothing else, `push notifications` using FireBase Clouding Messaging.

## About
Package for Pushing Notifications using Firebase Cloud Messaging Protocol.

Such as its name suggests, the package is intended for providing a simple way for
pushing notifications, and nothing else.
 
> Note: It has not any support for pushing `messages` or
notifications based on `topics`

Main features about SPN are:
- Intended for only push notifications - Keeping things simple!
- Push notifications are sent using only HTTP Protocol (https://firebase.google.com/docs/cloud-messaging/server)
- It is able for returning the FCM's raw response or a parsed one.
- Parsed responses have information about delivered / undelivered notifications,
  devices tokens that should be deleted, and about those ones should be resend.
- Also It has support for `Group Device Messaging`. See:
     - https://firebase.google.com/docs/cloud-messaging/android/device-group
     - https://firebase.google.com/docs/cloud-messaging/ios/device-group

For all about errors and exception triggering, please take a look at:
- https://firebase.google.com/docs/cloud-messaging/http-server-ref#table4
- https://firebase.google.com/docs/cloud-messaging/http-server-ref#table9

## Installation
Require the latest version of Spn into your project
 
```
composer require cobisja/spn
```

Or you can add it directly into your project's composer.json file:
```
{
    "require": {
        "cobisja/spn": "^0.1.0"
    }
}
```
### Laravel configuration
Register the Spn Service Provider into your app configuration file `config/app.php`:
```
'providers' => [
    // ...
    
    cobisja\Spn\SpnServiceProvider::class,
]
```
Then publish the package config file:
 ```
 php artisan vendor:publish
 ```
And finally in your `.env` file, add your Server's keys and project ID using the Keys you got from FCM:
 ```
 FCM_PROJECT_ID=<your_project_id>
 FCM_KEY=<your_fcm_server_key>
 ```
To get these keys, you must create a new application on the Firebase Cloud Messaging console.

## Getting started

Once everything is installed and configured, you can start to using all the helpers exposed by SPN,
to achieve all tasks related with
- Push notifications.
- Querying responses.
- Handling Exceptions.
- Device Group Management.

### Push Notifications
SPN can only handle **Notification Messages** via Firebase Http Protocol. The XMPP protocol is not currently supported.

#### Single Device
```php
 $response = push_notification(<device_token_id>, 'Greetings', 'Hi, from SPN :)');
```

#### Multiple devices
```php
$recipients = [device_token, device_token, ..., device_token] // Up to 1000 device tokens
$response = push_notification($repicpients, 'Greetings', 'Hi, from SPN :)');
```

#### Devices Group
```php
$response = push_notification(<devices_group_notification_key>, 'Greetings', 'Hi, from SPN :)');
```
### Handling responses
Once a notification message has been sent, SPN holds the FCM response, so you can querying it for useful information.
Check it out the following examples:

#### Getting the FCM's raw response
```php
$response = push_notification(<device_token>, 'Greetings', 'Hi, from SPN :)');
$rawResponse = $response->rawResponse();  
```
it returns:
```javascript
{"multicast_id":8724186164068196172,"success":1,"failure":0,"canonical_ids":0,"results":[{"message_id":"0:1488747479995544%6975114c6975114c"}]}
```
#### Response's stats
```php
$response = push_notification(<device_token>, 'Greetings', 'Hi, from SPN :)');
$stats = $response->stats();  
```
it returns:
```php
[
    'delivered' => [device_token, device_token, ..., device_token],
    'undelivered' => [device_token, device_token, ..., device_token],
    'shouldBeDeleted => [device_token, device_token, ..., device_token],
    'shouldBeResend' => [device_token, device_token, ..., device_token]
]
```
If you like to get only an specific stats information, you can use one of the following methods:

```php
$delivered = $response->delivered(); // delivered tokens devices array.
$undelivered = $response->undelivered(); // undelivered tokens devices array.
$tokensForDeletion = $response->tokensForDeletion(); // invalid tokens.
$tokensForResending = $response->tokensForResending(); // tokens for resending.
```
> Note: For notification messages sent to a Device Group, the `shouldBeDeleted`, and `shouldBeResend` always be `null`.

### Handling errors and Exceptions.

SPN config file `config/spn.php` defines 2 keys for specifying how errors should be handled:
- `on_error`: tell if an exception will be triggered when any of errors described in [Interpreting a downstream message response](https://firebase.google.com/docs/cloud-messaging/http-server-ref#table4) are found
- `on_partial_deliveries`: tell if an exception will be triggered when a notification cannot be delivered to all of its recipients. 

SPN defines the following exceptions:
- `ConnectionException`: Any exception thrown by `GuzzleClient` class.
- `InvalidServerKeyException`: There was an error authenticating the sender account.
- `BadRequestException`: Only applies for JSON requests. Indicates that the request could not be parsed as JSON, or it contained invalid fields
- `InvalidServerErrorException`: Any fatal error.
- `UndeliveredNotificationException:` Triggered when a notification was not delivered to any of its recipients (it requires setting `on_partial_deliveries = true` in `config/spn.php` )
- `NontificationKeyAlreadyException`: The Device Group cannot be created because it already exists.
- `NotificationKeyNotFound`: The Device Group Notification Key is invalid.

### Device Group Messaging
According FCM:

> "With device group messaging, you can send a single message to multiple instances of an app running on devices belonging to a group. Typically, "group" refers a set of different devices that belong to a single user. All devices in a group share a common notification key, which is the token that FCM uses to fan out messages to all devices in the group." 

SPN exposes helpers for Device Group Management tasks:

### Creating a Device Group
```php
$devices = [device_token, device_token, ...., device_token];
$response = dgm_create(
    $devices,
    'appUser-Chris'
);
```
it replies with a `notification_key`:
```javascript
{
   "notification_key": "APA91bGHXQBB...9QgnYOEURwm0I3lmyqzk2TXQ"
}
```
### Adding members to a Device Group
```php
$devices = [device_token, device_token, ...., device_token];
$notificationKey = '<group_notification_key>';
$notificationKeyName = 'appUser-Chris';
$response = dgm_add(
    $devices,
    $notificationKey,
    $notificationKeyName
);
```
it replies with a `notification_key`:
```javascript
{
   "notification_key": "APA91bGHXQBB...9QgnYOEURwm0I3lmyqzk2TXQ"
}
```
### Removing members from a Device Group
```php
$devices = [device_token, device_token, ...., device_token];
$notificationKey = '<group_notification_key>';
$notificationKeyName = 'appUser-Chris';
$response = dgm_remove(
    $devices,
    $notificationKey,
    $notificationKeyName
);
```
it replies with a `notification_key`:
```javascript
{
   "notification_key": "APA91bGHXQBB...9QgnYOEURwm0I3lmyqzk2TXQ"
}
```
## License
Copyright (c) 2017 - Cobis (jcobis@gmail.com)

MIT License

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
"Software"), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NON INFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
