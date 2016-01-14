# Kandy Package for Laravel 5
This Laravel package encapsulates Kandy’s JS SDK and Restful APIs. Kandy is a product by GENBAND (www.genband.com) that utilizes WebRTC to enable peer to peer audio and video calls and chat. SMS and PSTN calling support will be added to this package in the near future.

With this package, you can enable video and audio calling between two users that are logged into your Laravel application.

Think of pages where you anticipate users collaborating with each other, possibly to discuss content on those pages. Your users could start a video call with other online users and enhance the collaboration experience.

Home page: http://www.kandy.io/
 
---

## Requirements
---
* PHP 5.5.9+

## Package Setup
---

### Add kandylaravel to your composer.json file

```php
"require": {
    "laravel/framework": "5.1.*",
    "illuminate/html" : "~5.0",
    "toddish/verify": "~5",
    "kandy-io/kandy-laravel": "2.4.2"
    ... // Others
},
```
For Laravel 4.2, please view releases at https://github.com/Kandy-IO/kandy-laravel/releases (synced with branch *laravel4*).

Then, run a composer update on the command line from the root of your project:
	
```
composer update
```

### Configuration

Use an artisan command to publish public:

```
php artisan vendor:publish --force --provider=Kodeplus\Kandylaravel\KandylaravelServiceProvider --tag=public
```

Use an artisan command to publish migrations:

```
php artisan vendor:publish --force --provider=Kodeplus\Kandylaravel\KandylaravelServiceProvider --tag=migrations
```

Note: If you haven't run migrate the database tables for Verify, you need to run command line below:
```
php artisan vendor:publish --provider=Toddish\Verify\Providers\VerifyServiceProvider --tag=migrations
```

Use an artisan command to publish configuration:

```
php artisan vendor:publish --provider=Kodeplus\Kandylaravel\KandylaravelServiceProvider --tag=config
```

### Database Migration

Before run migrations, edit database config in config\database.php.
Migrate the database tables for kandylaravel. Run these on the command line from the root of your project:

```
php artisan migrate
```

Seed the database tables for kandylaravel. Run these on the command line from the root of your project:

```
php artisan db:seed
```

Configuration file will be generated at

```
    config\kandy-laravel.php
```

Login to [kandy.io](https://www.kandy.io) to retrieve the ```api key``` and ```domain_api_secret``` for the domain

### Provider and Alias

Define service provider and alias for Kandy in ```config\app.php```
```php
"providers" => [
    ...	// Others
    Kodeplus\Kandylaravel\KandylaravelServiceProvider::class,
],

// Other configurations
'aliases' => [
    ...	// Others
    'KandyLaravel'      => Kodeplus\Kandylaravel\Facades\KandyLaravel::class,
    'KandyVideo'        => Kodeplus\Kandylaravel\Facades\Video::class,
    'KandyButton'       => Kodeplus\Kandylaravel\Facades\Button::class,
    'KandyStatus'       => Kodeplus\Kandylaravel\Facades\Status::class,
    'KandyAddressBook'  => Kodeplus\Kandylaravel\Facades\AddressBook::class,
    'KandyChat'         => Kodeplus\Kandylaravel\Facades\Chat::class,
    'KandyLiveChat'     => Kodeplus\Kandylaravel\Facades\LiveChat::class,
    'KandyCoBrowsing'   => Kodeplus\Kandylaravel\Facades\CoBrowsing::class,
    'KandySms'          => Kodeplus\Kandylaravel\Facades\Sms::class
],
```

## Usage
---

### Register
Prepare Kandy css/javascript and log-in Kandy user who is associated with userId:

```php
{!! KandyLaravel::init($userId); !!}
```

### Use Kandy Widget:

**Kandy Video**: Make a Kandy video component (video call)
```php
{!! 
    KandyButton::videoCall(array(
        "id"      => "kandyVideoAnswerButton",
        "class"   => "myButtonStyle",
        "options" => array(
            "callOut"      => array(
                "id"       => "callOut",
                "label"    => "User to call",
                "btnLabel" => "Call"
            ),
            "calling"      => array(
                "id"       => "calling",
                "label"    => "Calling...",
                "btnLabel" => "End Call"
            ),
            "incomingCall" => array(
                "id"       => "incomingCall",
                "label"    => "Incoming Call",
                "btnLabel" => "Answer"
            ),
            "onCall"       => array(
                "id"       => "onCall",
                "label"    => "You're connected!",
                "btnLabel" => "End Call"
            ),
        )
    ))
 !!}

{!! 
    KandyVideo::show(
        array(
            "title"       => "Them",
            "id"          => "theirVideo",
            "class"       => "myVideoStyle",
            "htmlOptions" => array( // Example how to use inline stylesheet
                "style" => "width: 340px;
                height: 250px;
                background-color: darkslategray"
            )
        )
    )
 !!}

{!! 
    KandyVideo::show(
        array(
            "title"       => "Me",
            "id"          => "myVideo",
            "class"       => "myStyle",
            "htmlOptions" => array( // Example how to use inline stylesheet
                "style" => "width: 340px;
                height: 250px;
                background-color: darkslategray"
            )
        )
    )
 !!}
```

**Kandy Voice**: Make a Kandy voice call button component (voice call)
```php
{!! 
    KandyButton::voiceCall(
	    array(
	        "id" => "kandyVideoAnswerButton",
	        "class" => "myButtonStyle",
	        "htmlOptions" => array("style" => "border: 1px solid #ccc;"),
                "options" => array(
                    "callOut"      => array(
	                "id"       => "callOut",
	                "label"    => "User to call",
	                "btnLabel" => "Call"
	            ),
	            "calling"      => array(
                    "id"       => "calling",
	                "label"    => "Calling...",
	                "btnLabel" => "End Call"
	            ),
	            "incomingCall" => array(
	                "id"       => "incomingCall",
	                "label"    => "Incoming Call",
	                "btnLabel" => "Answer"
	            ),
	            "onCall"       => array(
	                "id"       => "onCall",
	                "label"    => "You're connected!",
	                "btnLabel" => "End Call"
	            ),
	        )
	    )
	)
 !!}
```
	
**Kandy Status**: Make a Kandy user status component (available, unavailable, awway, busy....). Kandy Status usually use with kandy address book component.
```php
{!! 
    KandyStatus::show(
        array(
            "title" => "My Status",
	        "id"    => "presence",
	        "class" => "myStatusStyle",
	    )
	)
 !!}
```
	
**Kandy Addressbook**: Make a Kandy address book component which list all friend in your contact.
```php
{!! 
    KandyAddressBook::show(
        array(
            "title" => "My Contact",
	        "id"    => "contactsAndDirSearch",
	        "class" => "myAddressBookStyle",
	    )
	)
!!}
```
	
**Kandy Chat**: Make a Kandy chat component which help you send instant message to your friend in contact.
```php
{!! 
    KandyChat::show(
        array(
            "id" => "myChat",
            "class" => "myChatStyle",
            "options" => array(
                "contact" => array(
                    "id" => "myContact",
                    "label" => "Contacts",
                ),
                "message" => array(
                    "id" => "myMessage",
                    "label" => "Messages",
                ),
                "user" => array(
                    "name" => KandyLaravel::getUser($userId)->user_id
                )
            )

        )
    )
!!}
```

**Kandy SMS**: Make a Kandy SMS component which help you send SMS to someone's phone number
```php
{!!
 KandySms::show(
     array(
         'class'         => 'kandyButton myButtonStyle smsContainer',
         'htmlAttr'      => array('style' => 'width:40%; margin-top:10px'),
         'options'       => array(
             'messageHolder' => 'Enter your message',
             'numberHolder'  => 'Enter your number',
             'btnSendId'     => 'btnSendSms',
             'btnSendLabel'  => 'Send Sms'
         )

     )
 )
!!}
```

**Kandy Co-browsing**: Make a Kandy co-browsing component which help you share your browser screen with your friends.
```php
{!!
KandyCoBrowsing::show(array(
    'holderId'                  => 'cobrowsing-holder',
    'btnTerminateId'            => 'btnTerminateSession',
    'btnStopId'                 => 'btnStopCoBrowsing',
    'btnLeaveId'                => 'btnLeaveSession',
    'btnStartBrowsingViewerId'  => 'btnStartCoBrowsingViewer',
    'btnStartCoBrowsingId'      => 'btnStartCoBrowsing',
    'btnConnectSessionId'       => 'btnConnectSession',
    'currentUser'               => KandyLaravel::getUser(Auth::user()->id),
    'sessionListId'             => 'openSessions'
));
!!}
```

**Kandy Live Chat**: Make a small widget to help you implement live chat, give your customers ability to chat with customer service agent.
```php
@if(Auth::check() == false)
    {!! KandyLiveChat::show(array(
        'registerForm'  => array(
            'email' => array(
                'label' => 'Email *',
                'class' => '',
            ),
            'name'  => array(
                'label' => 'Name *',
                'class' => ''
            )
        ),
        'agentInfo' => array(
            'avatar'    => asset('kandy-io/kandy-laravel/assets/img/icon-helpdesk.png'),
            'title'     => 'Support Agent',
        )
    )) !!}
@endif
```

## KANDY APIs
---

Refer to:  ```kandy-laravel\src\Kodeplus\Kandylaravel\KandyLaravel.php```

### Sync users from Kandy to local database table kandy_users

```php
KandyLaravel::syncUsers();
```

### List Kandy user from local table kandy_users or from remote

```php
/**
 * User Type Accepted:
 *
 * KandyLaravel::KANDY_USER_ALL : get all kandy users
 * KandyLaravel::KANDY_USER_ASSIGNED : get kandy users who are tied to a Laravel user
 * KandyLaravel::KANDY_USER_UNASSIGNED : get kandy users who are not tied to any Laravel users
 */
$userType = KandyLaravel::KANDY_USER_ALL;

/**
 * Loading Type:
 *
 * true : list users from Kandy server
 * false: list users from local kandy_user table
 */
$loadKandyUsersFromServer = false;

KandyLaravel::listUser($userType , $loadKandyUsersFromServer);
```

### Create a kandy user and assign it to application user id:

```php
// Get userId using Verify package, you may get it different ways if you use a different authentication package
// Eg: Sentry::getUser()->id; (using Sentry)
$application_user_id = Auth::user()->id;

$kandy_user_id = "demo";
$kandy_email   = "demo@gmail.com";

KandyLaravel::createUser($kandy_user_id, $kandy_email, $application_user_id);
```

### Assign application user id to kandy user id:

```php
// Get userId using Verify package, you may get it different ways if you use a different authentication package
// Eg: Sentry::getUser()->id; (using Sentry)
$application_user_id = Auth::user()->id;

// kandy user
$kandy_user_id = "demo";

KandyLaravel::assignUser($application_user_id, $kandy_user_id);
```