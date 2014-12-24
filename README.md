kandylaravel v1
============

I. REQUIREMENTS
============
* PHP 5.4+

II. PACKAGE SETUP
============
1. Create a database for your application. Update the config file ```app/config/database.php```. Default configuration:
```
			'driver'    => 'mysql',
			'host'      => 'localhost',
			'database'  => 'kandylaravel',
			'username'  => 'root',
			'password'  => '',
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => '',
```

2. Declare kandylaravel package in composer.json:
```
"require": {
		"laravel/framework": "4.2.*",
        "toddish/verify": "3.*", // This is one user login package, you could use others
        "kodeplusdev/kandylaravel": "dev-master"
	},
```
And run the composer to download the package:
		composer update

3. Run the database migrate to create some db tables:
		php artisan migrate --package="kodeplusdev/kandylaravel"
        
4. Run the public command to public default js/css and prepare the config file:
		php artisan asset:publish kodeplusdev/kandylaravel
        
5. Config file will be generated at 
    	app\config\packages\kodeplusdev\kandylaravel\config.php

	Login to kandy.io to retrieve the ```api key``` and ```domain_api_secret``` for the domain

6. Define service provider and alias for Kandy in ```kandylaravel\app\config\app.php```

```php
"providers" => array(
		...	// Others
        'Kodeplusdev\Kandylaravel\KandylaravelServiceProvider',
),
// Other configs
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
III. HOW TO USE KANDY WIDGETS
============
1. Prepare Kandy css/javascript and log-in Kandy user who is associated with userId:
		{{KandyLaravel::init($userId);}}

2. Use Kandy Widget anywhere on the page:

a) Kandy Video
```php
       {{KandyButton::videoCall(array(
            "id" => "kandyVideoAnswerButton",
            "class" => "myButtonStyle",
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

        {{KandyVideo::show(
            array(
                "title" => "Them",
                "id" => "theirVideo",
                "class" => "myVideoStyle",
                "htmlOptions" =>
                    array(
                        "style" => "width: 340px;
                                    height: 250px;
                                    background-color: darkslategray"
                    )
            )
        )}}

        {{
            KandyVideo::show(
                 array(
                    "title" => "Me",
                    "id" => "myVideo",
                    "class" => "myStyle",
                    "htmlOptions" =>
                        array(
                        "style" => "width: 340px;
                                    height: 250px;
                                    background-color: darkslategray"
                        )
                 )
            )
        }}
```
b) Kandy Voice
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
c) Kandy Status
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
d) Kandy Addressbook
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
e) Kandy Chat
```php
    {{
    KandyChat::show(
            array(
                "id"      => "myChat",
                "class"   => "myChatStyle",
                "options" => array(
                    "contact"   => array(
                        "id"    => "myContact",
                        "label" => "Contacts",
                    ),
                    "message"   => array(
                        "id"    => "myMessage",
                        "label" => "Messages",
                    ),
                    "user"      => array(
                        "name"  => KandyLaravel::getUser($userId)->user_id
                    )
                )

            )
        )
    }}
```
IV. HOW TO USE KANDY APIs
============
Refer to: ```kandylaravel\src\Kodeplusdev\Kandylaravel\KandyLaravel.php```

1. Sync users from Kandy to local database table kandy_users

		KandyLaravel::syncUsers()

2. List Kandy user from local table kandy_users or from remote

		KandyLaravel::listUser(KandyLaravel::KANDY_USER_ALL, $remote = false)

3. Create a kandy user and assign it to application user id:

		KandyLaravel::createUser($kandy_user_id, $kandy_email, $application_user_id);

4. Assign application user id to kandy user id:

		KandyLaravel::assignUser($application_user_id, $kandy_user_id)
