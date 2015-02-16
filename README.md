# Kandy Laravel Package
This is a Laravel package that encapsulate all Kandy JS SDK and Kandy Restful APIs. Kandy is a product by Gendband which utilizes WebRTC to enable peer to peer video, audio, chat, SMS and PSTN.

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
    "kodeplusdev/kandylaravel": "dev-master"
},
```

Then, run a composer update on the command line from the root of your project:
	
```
composer update
```

### Database Migration

Migrate the database tables for kandylaravel. Run these on the command line from the root of your project:

```
php artisan migrate --package="kodeplusdev/kandylaravel"
```

### Configuration

Configuration file is be generated at

```
    app\config\packages\kodeplusdev\kandylaravel\config.php
```

Login to [kandy.io](https://www.kandy.io) to retrieve the ```api key``` and ```domain_api_secret``` for the domain

### Provider and Alias

Define service provider and alias for Kandy in ```kandylaravel\app\config\app.php```
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
    'KandyLaravel'      => 'Kodeplusdev\Kandylaravel\Facades\KandyLaravel',
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

## KANDY APIs
---

Refer to:  ```kandylaravel\src\Kodeplusdev\Kandylaravel\KandyLaravel.php```

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
