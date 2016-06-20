# Kandy Laravel Package
This Laravel package encapsulates Kandy’s JS SDK and Restful APIs. Kandy is a product by GENBAND (www.genband.com) that utilizes WebRTC to enable peer to peer audio and video calls, chat, SMS and PSTN calling.

With this package, you can enable video and audio calling between two users that are logged into your Laravel application.

Think of pages where you anticipate users collaborating with each other, possibly to discuss content on those pages. Your users could start a video call with other online users and enhance the collaboration experience.

Home page: http://www.kandy.io/
 
---

## Requirements
---
* PHP 5.4+

## Package Setup
---

### Add kandylaravel to your composer.json file

```php
"require": {
    "laravel/framework": "4.2.*",
    "toddish/verify": "3.*",
    ... // Others
    "kandy-io/kandy-laravel": "2.3.2"
},
```

Then, run a composer update on the command line from the root of your project:
	
```
composer update
```

### Database Migration

Migrate the database tables for kandylaravel. Run these on the command line from the root of your project:

```
php artisan migrate --package="kandy-io/kandy-laravel"
```

### Configuration

Use an artisan command to publish configuration:

```
php artisan config:publish kandy-io/kandy-laravel
```

Configuration file will be generated at

```
    app\config\packages\kandy-io\kandy-laravel\config.php
```

Login to [kandy.io](https://www.kandy.io) to retrieve the ```api key``` and ```domain_api_secret``` for the domain

### Provider and Alias

Define service provider and alias for Kandy in ```app\config\app.php```
```php
"providers" => array(
    ...	// Others
    'Kodeplusdev\Kandylaravel\KandylaravelServiceProvider',
),

// Other configurations
'aliases' => array(
    ...	// Others
    'KandyVideo'        => 'Kodeplusdev\Kandylaravel\Facades\Video',
    'KandyButton'       => 'Kodeplusdev\Kandylaravel\Facades\Button',
    'KandyStatus'       => 'Kodeplusdev\Kandylaravel\Facades\Status',
    'KandyAddressBook'  => 'Kodeplusdev\Kandylaravel\Facades\AddressBook',
    'KandyChat'         => 'Kodeplusdev\Kandylaravel\Facades\Chat',
    'KandyLiveChat'     => 'Kodeplusdev\Kandylaravel\Facades\LiveChat',
    'KandyLaravel'      => 'Kodeplusdev\Kandylaravel\Facades\KandyLaravel',
    'KandyCoBrowsing'   => 'Kodeplusdev\Kandylaravel\Facades\CoBrowsing',
    'KandySms'          => 'Kodeplusdev\Kandylaravel\Facades\Sms'
),
```

## Usage
---

### Register
Prepare Kandy css/javascript and log-in Kandy user who is associated with userId:

```php
{{KandyLaravel::init($userId);}}
```

### Use Kandy Widget:

**Kandy Video**
```php
{{
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
}}

{{
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
}}

{{
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
}}
```

**Kandy Voice**
```php
{{
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
}}
```
	
**Kandy Status**
```php
{{
    KandyStatus::show(
        array(
            "title" => "My Status",
	        "id"    => "presence",
	        "class" => "myStatusStyle",
	    )
	)
}}
```
	
**Kandy Addressbook**
```php
{{
    KandyAddressBook::show(
        array(
            "title" => "My Contact",
	        "id"    => "contactsAndDirSearch",
	        "class" => "myAddressBookStyle",
	    )
	)
}}
```
	
**Kandy Chat**
```php
{{
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
}}
```

**Kandy Live Chat**
```php
{{ KandyLiveChat::show(array(
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
        'avatar'    => asset('packages/kandy-io/kandy-laravel/assets/img/icon-helpdesk.png'),
        'title'     => 'Support Agent',
    )
)) }}
```

**Kandy Co-Browsing**
```php
{{ KandyCoBrowsing::show(
    array(
        'holderId'                  => 'cobrowsing-holder',
        'btnTerminateId'            => 'btnTerminateSession',
        'btnStopId'                 => 'btnStopCoBrowsing',
        'btnLeaveId'                => 'btnLeaveSession',
        'btnStartBrowsingViewerId'  => 'btnStartCoBrowsingViewer',
        'btnStartCoBrowsingId'      => 'btnStartCoBrowsing',
        'btnConnectSessionId'       => 'btnConnectSession',
        'currentUser'               => KandyLaravel::getUser($userId),
        'sessionListId'             => 'openSessions'
    ))
}}
```

**Kandy Sms**
```php
{{ KandySms::show(
    array(
        'class'         => 'kandyButton myButtonStyle smsContainer',
        'htmlAttr'      => array('style' => 'width:40%; margin-top:10px'),
        'options'       => array(
            'messageHolder' => 'Enter your message',
            'numberHolder'  => 'Enter your number',
            'btnSendId'     => 'btnSendSms',
            'btnSendLabel'  => 'Send Sms'
        )

    ))
}}
```

## KANDY APIs
---

Refer to:  ```kandy-laravel\src\Kodeplusdev\Kandylaravel\KandyLaravel.php```

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
