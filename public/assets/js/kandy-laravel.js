//========================KANDY SETUP AND LISTENER CALLBACK ==============

var unassignedUser = "KANDY UNASSIGNED USER";
var chatMessageTimeStamp = 0;
var activeContainerId;

// Create audio objects to play incoming calls and outgoing calls sound
var $audioRingIn = jQuery('<audio>', { loop: 'loop', id: 'ring-in' });
var $audioRingOut = jQuery('<audio>', { loop: 'loop', id: 'ring-out' });
var bindedCloseChatEvent = false;
// Keep track of the callId.
var callId;
// Keep track of screen sharing status.
var isSharing = false;

// Load audio source to DOM to indicate call events
var audioSource = {
    ringIn: [
        { src: 'https://kandy-portal.s3.amazonaws.com/public/sounds/ringin.mp3', type: 'audio/mp3' },
        { src: 'https://kandy-portal.s3.amazonaws.com/public/sounds/ringin.ogg', type: 'audio/ogg' }
    ],
    ringOut: [
        { src: 'https://kandy-portal.s3.amazonaws.com/public/sounds/ringout.mp3', type: 'audio/mp3' },
        { src: 'https://kandy-portal.s3.amazonaws.com/public/sounds/ringout.ogg', type: 'audio/ogg' }
    ]
};

audioSource.ringIn.forEach(function (entry) {
    var $source = jQuery('<source>').attr('src', entry.src);
    $audioRingIn.append($source);
});

audioSource.ringOut.forEach(function (entry) {
    var $source = jQuery('<source>').attr('src', entry.src);
    $audioRingOut.append($source);
});


/**
 * Kandy Set up
 */
setup = function () {
    // initialize KandyAPI.Phone, passing a config JSON object that contains listeners (event callbacks)
    kandy.setup({
        // respond to Kandy events...
        remoteVideoContainer: jQuery('#theirVideo')[0],
        localVideoContainer: jQuery('#myVideo')[0],
        listeners: {
            callinitiated: kandy_on_call_initiate_callback,
            callincoming: kandy_incoming_call_callback,
            // when an outgoing call is connected
            oncall: kandy_on_call_callback,
            // when an incoming call is connected
            // you indicated that you are answering the call
            callanswered: kandy_call_answered_callback,
            callended: kandy_call_ended_callback,
            // Media Event
            media: kandy_on_media_error,
            // Screensharing Event
            callscreenstopped: kandy_on_stop_success
        },
        // Reference the default Chrome extension.
        chromeExtensionId: {
            chromeExtensionId: 'daohbhpgnnlgkipndobecbmahalalhcp'
        }
    });

    if(jQuery(".kandyChat").length){
        kandy.setup({
            listeners: {
                message: kandy_onMessage,
                chatGroupMessage: kandy_onGroupMessage,
                chatGroupInvite: kandy_onGroupInvite,
                chatGroupBoot: kandy_onRemovedFromGroup,
                chatGroupLeave: kandy_onLeaveGroup,
                chatGroupUpdate: '',
                chatGroupDelete: kandy_onTerminateGroup
            }
        })
    }

};

/**
 * Login Success Callback.
 */
kandy_login_success_callback = function () {
    console.log('login successful');
    //have kandyAddressBook widget
    if (jQuery(".kandyAddressBook").length) {
        kandy_loadContacts_addressBook();
        kandy_refresh_addressBook();
    }
    //have kandyChat widget
    if (jQuery(".kandyChat").length) {
        kandy_loadContacts_chat();
        kandy_loadGroups();
        setTimeout(updateUserGroupStatus,3000);
        setTimeout(kandy_refresh_chat, 10000);
    }
    if(jQuery("#coBrowsing").length){
        kandy_getOpenSessionsByType("cobrowsing",loadSessionList);
    }

    //call user callback
    if (typeof login_success_callback == 'function') {
        login_success_callback();
    }

    //call user logout if exists
    if (typeof kandy_logout == 'function') {
        kandy_logout();
    }
};

/**
 * Login Fail Callback
 */
kandy_login_failed_callback = function () {
    console.log('login error');
    if (typeof login_failed_callback == 'function') {
        login_failed_callback();
    }
};

/**
 * Status Notification Callback.
 *
 * @param userId
 * @param state
 * @param description
 */
kandy_presence_notification_callback = function (userId, state, description) {
    // HTML id can't contain @ and jquery doesn't like periods (in id)
    var id_attr = '.kandyAddressBook .kandyAddressContactList #presence_' + userId.replace(/[.@]/g, '_');
    jQuery(id_attr).text(description);
    if (typeof presence_notification_callback == 'function') {
        presence_notification_callback(userId, state, description);
    }
    //update chat status
    if(jQuery('.kandyChat').length >0){
        var liUser = jQuery('.kandyChat .cd-tabs-navigation li#' +userId.replace(/[.@]/g, '_'));
        var statusItem = liUser.find('i.status');
        statusItem.text(description);

        liUser.removeClass().addClass('kandy-chat-status-' + description.replace(/ /g,'-').toLowerCase());
        liUser.attr('title', description);
    }
    usersStatus[userId] = description;
    updateUserGroupStatus();
};
/**
 * on call initiate callback
 * @param call
 */
kandy_on_call_initiate_callback = function(call){
    // Store the callId.
    callId = call.getId();
    jQuery('#'+activeContainerId).attr('data-call-id', callId);
    $audioRingIn[0].pause();
    $audioRingOut[0].play();
};

/**
 * OnCall Callback
 * @param call
 */
kandy_on_call_callback = function (call) {
    if (typeof on_call_callback == 'function') {
        on_call_callback(call);
    }

    $audioRingOut[0].pause();
    callId = call.getId();
    var target = jQuery('.kandyButton[data-call-id="'+callId+'"]');
    changeAnswerButtonState("ON_CALL",target);
};

/**
 * Incoming Callback.
 *
 * @param call
 * @param isAnonymous
 */
kandy_incoming_call_callback = function (call, isAnonymous) {
    if (typeof incoming_call_callback == 'function') {
        incoming_call_callback(call, isAnonymous);
    }

    $audioRingIn[0].play();

    var target = jQuery('.kandyVideoButtonCallOut:visible').get(0).closest('.kandyButton');
    callId = call.getId();
    jQuery(target).attr('data-call-id', callId);
    changeAnswerButtonState('BEING_CALLED', target);
};

/**
 * Kandy call answered callback.
 *
 * @param call
 * @param isAnonymous
 */
kandy_call_answered_callback = function (call, isAnonymous) {
    if (typeof call_answered_callback == 'function') {
        call_answered_callback(call, isAnonymous);
    }

    $audioRingOut[0].pause();
    $audioRingIn[0].pause();
    callId = call.getId();
    var target = jQuery('.kandyButton[data-call-id="'+callId+'"]');
    changeAnswerButtonState("ON_CALL", target);
};

kandy_call_answer_failed_callback = function (call){
    callId = null;
    console.log('call answer failed', call);
}

/**
 * Kandy call ended callback.
 *
 */
kandy_call_ended_callback = function (call) {
    $audioRingOut[0].play();
    $audioRingIn[0].pause();
    callId = null;
    if (typeof call_ended_callback == 'function') {
        call_ended_callback();
    }
    var target = jQuery('.kandyButton[data-call-id="'+ call.getId() +'"]');
    changeAnswerButtonState("READY_FOR_CALLING",target);
};

/**
 * Change AnswerButtonState with KandyButton Widget.
 * @param target
 * @param state
 */
changeAnswerButtonState = function (state, target) {
    var kandyButton = (typeof target !== 'undefined')?jQuery(target):jQuery(".kandyButton");

    switch (state) {
        case 'READY_FOR_CALLING':
            $audioRingIn[0].pause();
            $audioRingOut[0].pause();
            kandyButton.find('.kandyVideoButtonSomeonesCalling').hide();
            kandyButton.find('.kandyVideoButtonCallOut').show();
            kandyButton.find('.kandyVideoButtonCalling').hide();
            kandyButton.find('.kandyVideoButtonOnCall').hide();
            break;

        case 'BEING_CALLED':
            kandyButton.find('.kandyVideoButtonSomeonesCalling').show();
            kandyButton.find('.kandyVideoButtonCallOut').hide();
            kandyButton.find('.kandyVideoButtonCalling').hide();
            kandyButton.find('.kandyVideoButtonOnCall').hide();
            break;

        case 'CALLING':
            kandyButton.find('.kandyVideoButtonSomeonesCalling').hide();
            kandyButton.find('.kandyVideoButtonCallOut').hide();
            kandyButton.find('.kandyVideoButtonCalling').show();
            kandyButton.find('.kandyVideoButtonOnCall').hide();
            break;
        case 'ON_CALL':
            kandyButton.find('.kandyVideoButtonSomeonesCalling').hide();
            kandyButton.find('.kandyVideoButtonCallOut').hide();
            kandyButton.find('.kandyVideoButtonCalling').hide();
            kandyButton.find('.kandyVideoButtonOnCall').css('display', 'inline-block');
            break;
    }
};

/**
 * Event when answer a call.
 *
 * @param target
 */
kandy_answer_video_call = function (target) {
    var kandyButtonId = jQuery(target).data('container');
    var currentCallId = jQuery('div#'+kandyButtonId).attr('data-call-id');
    activeContainerId = kandyButtonId;
    callId = currentCallId;
    kandy.call.answerCall(currentCallId, true);
    if (typeof answer_video_call_callback == 'function') {
        answer_video_call_callback("ANSWERING_CALL");
    }
};

/*
 Event when click call button
 */
kandy_make_video_call = function (target) {
    var kandyButtonId = jQuery(target).data('container');
    activeContainerId = kandyButtonId;

    kandy.call.makeCall(jQuery('#'+kandyButtonId+' .kandyVideoButtonCallOut #callOutUserId').val(),true);
    changeAnswerButtonState("CALLING", '#'+ kandyButtonId);
    if (typeof make_video_call_callback == 'function') {
        make_video_call_callback(target);
    }
};

/*
 Event when answer a voice call
 */
kandy_answer_voice_call = function (target) {
    var kandyButtonId = jQuery(target).data('container');
    var currentCallId = jQuery('div#'+kandyButtonId).attr('data-call-id');
    activeContainerId = kandyButtonId;
    kandy.call.answerCall(currentCallId, false);
    if (typeof answer_voice_call_callback == 'function') {
        answer_voice_call_callback(target);
    }

};

/*
 Event when click call button
 */
kandy_make_voice_call = function (target) {
    var kandyButtonId = jQuery(target).data('container');
    activeContainerId = kandyButtonId;
    kandy.call.makeCall(jQuery('#'+kandyButtonId+' .kandyVideoButtonCallOut #callOutUserId').val(),false);
    changeAnswerButtonState("CALLING", '#'+kandyButtonId);

    if (typeof make_voice_call_callback == 'function') {
        make_voice_call_callback(target);
    }
};

/*
 Event when click end call button
 */
kandy_end_call = function (target) {

    var kandyButtonId = jQuery(target).data('container');
    var currentCallId = jQuery('div#'+kandyButtonId).attr('data-call-id');

    kandy.call.endCall(currentCallId);
    if (callId) {
        callId = null;
    }
    if (typeof end_call_callback == 'function') {
        end_call_callback(target);
    }

    changeAnswerButtonState("READY_FOR_CALLING", "#"+kandyButtonId);

    // Update screensharing status.
    isSharing = false;
};

/*-------------Screen Sharing--------------*/

// Called when the media event is triggered.
function kandy_on_media_error(error) {
    switch(error.type)
    {
        case kandy.call.MediaErrors.NOT_FOUND:
            console.log("No WebRTC support was found.");
            break;
        case kandy.call.MediaErrors.NO_SCREENSHARING_WARNING:
            console.log("WebRTC supported, but no screensharing support was found.");
            break;
        default:
            console.log('Other error or warning encountered.');
            break;
    }
}

// Executed when the user clicks on the 'Toggle Screensharing' button.
toggle_screen_sharing = function () {
    // Check if we should start or stop sharing.
    if(callId && isSharing) {
        // Stop screensharing.
        kandy.call.stopScreenSharing(callId, kandy_on_stop_success, kandy_on_stop_failure);
    } else {
        // Start screensharing.
        kandy.call.startScreenSharing(callId, kandy_on_start_success, kandy_on_start_failure);
    }
};

// What to do on a successful screenshare start.
function kandy_on_start_success() {
    console.log('Screensharing started.');
    jQuery('.btnScreenSharing').val('Stop Screen Sharing');
    isSharing = true;
}

// What to do on a failed screenshare start.
function kandy_on_start_failure() {
    console.log('Failed to start screensharing.');
}

// What to do on a successful screenshare stop.
function kandy_on_stop_success() {
    console.log('Screensharing stopped.');
    jQuery('.btnScreenSharing').val('Screen Sharing');
    isSharing = false;
}

// What to do on a failed screenshare stop.
function kandy_on_stop_failure() {
    console.log('Failed to stop screensharing.');
}
/*------------------End screen sharing------------------*/

/**
 * ADDRESS BOOK WIDGET
 */
/**
 * Load contact list for addressBook widget
 */
kandy_loadContacts_addressBook = function () {
    var contactListForPresence = [];
    var contactToRemove = [];
    kandy.addressbook.retrievePersonalAddressBook(
        function (results) {
            results = getDisplayNameForContact(results);
            // clear out the current address book list
            jQuery(".kandyAddressBook .kandyAddressContactList div:not(:first)").remove();
            var div = null;
            if (results.length == 0) {
                div = "<div class='kandyAddressBookNoResult'>-- No Contacts --</div>";
                jQuery('.kandyAddressBook .kandyAddressContactList').append(div);
                var full_user_id = $('#full_user_id').val();
                kandy.getLastSeen([full_user_id],
                    function(results) {
                        console.log('Update time last seen');
                    },
                    function() {
                        console.log('Error occur when update time last seen');
                    }
                );
            } else {
                jQuery('.kandyAddressBook .kandyAddressContactList').append("<div class='kandy-contact-heading'><span class='displayname'><b>Username</b></span><span class='userid'><b>Contact</b></span><span class='presence_'><b>Status</b></span></div>");

                for (var i = 0; i < results.length; i++) {
                    var displayName = results[i].display_name;
                    var contactId = results[i].contact_id;

                    if (displayName == unassignedUser) {
                        contactToRemove.push(contactId);
                        continue;
                    }
                    contactListForPresence.push({full_user_id: results[i].contact_user_name});

                    var id_attr = results[i].contact_user_name.replace(/[.@]/g, '_');
                    jQuery('.kandyAddressBook .kandyAddressContactList').append(
                        // HTML id can't contain @ and jquery doesn't like periods (in id)
                        "<div class='kandyContactItem' id='uid_" + results[i].contact_user_name.replace(/[.@]/g, '_') + "'>" +
                        "<span class='displayname'>" + displayName + "</span>" +
                        "<span class='userId'>" + results[i].contact_user_name + "</span>" +
                        "<span id='presence_" + id_attr + "' class='presence'></span>" +
                        "<input class='removeBtn' type='button' value='Remove' " +
                        " onclick='kandy_removeFromContacts(\"" + contactId + "\")'>" +
                        "</div>"
                    );
                }

                for (var i = 0; i < contactToRemove.length; i++) {
                    kandy_removeFromContacts(contactToRemove[i]);
                }
            }
        },
        function () {
            console.log("Error kandy_loadContacts_addressBook");
        }
    );
};

kandy_refresh_addressBook = function () {
    var arrayUserIds = [$('#full_user_id').val()];
    $( "span.userId" ).each(function() {
        var userId = $(this).text().trim();
        if(arrayUserIds.indexOf(userId) < 0) {
            arrayUserIds.push(userId);
        }
    });
    if(arrayUserIds.length > 0) {
        kandy.getLastSeen(arrayUserIds,
            function(resultsLastSeen) {
                var server_timestamp = resultsLastSeen.server_timestamp;
                var users = resultsLastSeen.users;
                var full_user_id = null;
                var id_attr = null;
                var last_seen = 0;
                for (var i = 0; i < users.length; i++) {
                    full_user_id = users[i].full_user_id;
                    last_seen = users[i].last_seen;
                    id_attr = full_user_id.replace(/[.@]/g, '_');
                    //Is Online
                    if(parseInt(server_timestamp) - parseInt(last_seen) < 10000) {
                        $.ajax({
                            url: baseUrl + '/kandy/getPresenceStatus',
                            data: {full_user_id : full_user_id, _token : _token},
                            type: 'POST',
                            success: function (res){
                                if(res.status == 'success'){
                                    $('#presence_' + res.data.full_user_id.replace(/[.@]/g, '_')).text(res.data.presence_text);
                                } else {
                                    console.log(res.message);
                                }
                            },
                            error: function() {
                                console.log('Error occur when getPresenceStatus');
                            }
                        });
                    } else {
                        $('#presence_' + id_attr).text('Offline');
                    }
                }
            }, function () {
                console.log("Error kandy_refresh_addressBook");
            }
        );
    }
    setTimeout(kandy_refresh_addressBook, 10000);
};
/**
 * Change current user status with kandyAddressBook
 *
 * @param status
 */
kandy_my_status_changed = function (status) {
    $.ajax({
        url: baseUrl + '/kandy/updatePresence',
        data: {status : status, _token : _token},
        type: 'POST',
        success: function (res){
            if(res.status == 'success') {
                console.log('update presence status is successfully');
                kandy_refresh_addressBook();
            } else {
                console.log(res.message);
            }
        },
        error: function() {
            console.log('Error occur when update presence status');
        }
    });
};

/**
 * Add a user to contact list with kandyAddressBook
 * @type {null}
 */
var userIdToAddToContacts = null;  // need access to this in anonymous function below
kandy_addToContacts = function (userId) {
    userIdToAddToContacts = userId;

    // HTML id can't contain @ and jquery doesn't like periods (in id)
    if (jQuery('#uid_' + userId.replace(/[.@]/g, '_')).length > 0) {
        alert("This person is already in your contact list.")
    } else {
        // get and AddressBook.Entry object for this contact
        kandy.addressbook.searchDirectoryByUserName(
                userId,
                function (results) {
                    for (var i = 0; i < results.length; ++i) {
                        if (results[i].full_user_id === userIdToAddToContacts) {
                            // user name and nickname are required
                            var contact = {
                                contact_user_name: results[i].full_user_id,
                                contact_nickname: results[i].full_user_id
                            };
                            if (results[i].user_first_name) {
                                contact['contact_first_name'] = results[i].user_first_name;
                            }
                            if (results[i].user_last_name) {
                                contact['contact_last_name'] = results[i].user_last_name;
                            }
                            if (results[i].user_phone_number) {
                                contact['contact_home_phone'] = results[i].user_phone_number;
                            }
                            if (results[i].user_email) {
                                contact['contact_email'] = results[i].user_email;
                            }

                            kandy.addressbook.addToPersonalAddressBook(
                                    contact,
                                    kandy_loadContacts_addressBook, // function to call on success
                                    function (message) {
                                        alert("Error: " + message);
                                    }
                            );
                            break;
                        }
                    }
                },
                function (statusCode) {
                    console.log("Error getting contact details: " + statusCode)
                }
        );
    }
};

/**
 * Remove a user from Contact List with kandyAddressBook
 * @param nickname
 */
kandy_removeFromContacts = function (nickname) {
    kandy.addressbook.removeFromPersonalAddressBook(nickname,
            kandy_loadContacts_addressBook,  // function to call on success
            function () {
                console.log('Error kandy_removeFromContacts ');
            }
    );
};

/**
 * Search contact list by username with kandyAddressBook
 */
kandy_searchDirectoryByUserName = function () {
    var userName = jQuery('.kandyAddressBook .kandyDirectorySearch #kandySearchUserName').val();
    $.ajax({
        url: baseUrl + "/kandy/getUsersForSearch",
        data: {q:userName, _token: _token}
    }).done(function (results) {
        jQuery(".kandyAddressBook .kandyDirSearchResults div:not(:first)").remove();
        var div = null;
        if (results.length == 0) {
            div = "<div class='kandyAddressBookNoResult'>-- No Matches Found --</div>";
            jQuery('.kandyAddressBook .kandyDirSearchResults').append(div);
        } else {
            for (var i = 0; i < results.length; i++) {
                jQuery('.kandyDirSearchResults').append(
                        "<div class='kandySearchItem'>" +
                        "<span class='userId'>" + results[i].main_username + "</span>" +
                        "<input type='button' value='Add Contact' onclick='kandy_addToContacts(\"" +
                        results[i].kandy_full_username + "\")' />" +
                        "</div>"
                );
            }
        }
    }).fail(function() {
        jQuery(".kandyAddressBook .kandyDirSearchResults div:not(:first)").remove();
        var div = "<div class='kandyAddressBookNoResult'>There was an error with your request.</div>";
        jQuery('.kandyAddressBook .kandyDirSearchResults').append(div);
    });
};

/**
 * ===================KANDY CHAT WIDGET FUNCTION ==========================
 */

/**
 * Add an example chat box
 */
var addExampleBox = function () {
    var tabId = "example";
    tabContentWrapper.append(getLiContent(tabId));
    tabContentWrapper.find('li[data-content="' + tabId + '"]').addClass('selected').find(".chat-input").attr('disabled', true);
};

/**
 * Get display name for chat content
 *
 * @param data
 * @returns {*}
 */
var getDisplayNameForChatContent = function (msg) {
    if (msg) {
        if(displayNames.hasOwnProperty(msg.sender.full_user_id)){
            msg.sender.display_name = displayNames[msg.sender.full_user_id];
            msg.sender.contact_user_name = msg.sender.full_user_id;
        } else {
            $.ajax({
                url: baseUrl + "/kandy/getNameForChatContent",
                type: "POST",
                data: {data:msg, _token: _token},
                async: false
            }).done(function(response) {
                msg = response;
            }).fail(function(e) {
            });
        }
    }
    return msg;
};

/**
 * Get display name for contacts
 *
 * @param data
 * @returns {*}
 */
var getDisplayNameForContact = function (data) {
    if (data.length) {
        jQuery.ajax({
            url: baseUrl + "/kandy/getNameForContact",
            data: {data: data, _token: _token},
            async: false,
            type: "POST"
        }).done(function (response) {
            data = response;
        }).fail(function (e) {
        });
    }
    return data;
};

/**
 * Load Contact for KandyChat
 */
kandy_loadContacts_chat = function () {
    var contactListForPresence = [];
    kandy.addressbook.retrievePersonalAddressBook(
        function (results) {
            results = getDisplayNameForContact(results);
            emptyContact();
            for (var i = 0; i < results.length; i++) {
                prependContact(results[i]);
                if(!displayNames.hasOwnProperty(results[i].contact_user_name)){
                    displayNames[results[i].contact_user_name] = results[i].display_name;
                }
                contactListForPresence.push(results[i].contact_user_name);
            }

            if(contactListForPresence.length > 0) {
                kandy.getLastSeen(contactListForPresence,
                    function (resultsLastSeen) {
                        var server_timestamp = resultsLastSeen.server_timestamp;
                        var users = resultsLastSeen.users;
                        var full_user_id = null;
                        var id_attr = null;
                        var last_seen = 0;
                        for (var i = 0; i < users.length; i++) {
                            full_user_id = users[i].full_user_id;
                            last_seen = users[i].last_seen;
                            id_attr = full_user_id.replace(/[.@]/g, '_');
                            //Is Online
                            if (parseInt(server_timestamp) - parseInt(last_seen) < 10000) {
                                $.ajax({
                                    url: baseUrl + '/kandy/getPresenceStatus',
                                    data: {full_user_id: full_user_id, _token: _token},
                                    type: 'POST',
                                    success: function (res) {
                                        if (res.status == 'success') {
                                            kandy_presence_notification_callback(res.data.full_user_id, res.data.presence_status, res.data.presence_text);
                                        } else {
                                            console.log(res.message);
                                        }
                                    },
                                    error: function () {
                                        console.log('Error occur when getPresenceStatus');
                                    }
                                });
                            } else {
                                kandy_presence_notification_callback(id_attr, -1, "Offline");
                            }
                        }
                    }, function () {
                        console.log("Error occur when call function getLastSeen");
                    }
                );
            }

            addExampleBox();
        },
        function () {
            console.log("Error");
            addExampleBox();
        }
    );

};

kandy_refresh_chat = function() {
    var arrayUserIds = [];
    $( "ul.contacts > li > a" ).each(function() {
        var userId = $(this).data('content');
        arrayUserIds.push(userId);
    });
    if(arrayUserIds.length > 0) {
        kandy.getLastSeen(arrayUserIds,
            function(resultsLastSeen) {
                var server_timestamp = resultsLastSeen.server_timestamp;
                var users = resultsLastSeen.users;
                var full_user_id = null;
                var id_attr = null;
                var last_seen = 0;
                for (var i = 0; i < users.length; i++) {
                    full_user_id = users[i].full_user_id;
                    last_seen = users[i].last_seen;
                    id_attr = full_user_id.replace(/[.@]/g, '_');
                    //Is Online
                    if(parseInt(server_timestamp) - parseInt(last_seen) < 10000) {
                        $.ajax({
                            url: baseUrl + '/kandy/getPresenceStatus',
                            data: {full_user_id : full_user_id, _token : _token},
                            type: 'POST',
                            success: function (res){
                                if(res.status == 'success'){
                                    var class_status = 'kandy-chat-status-' + res.data.presence_text.toLowerCase().replace(/\s/g, '-');
                                    $('#' + res.data.full_user_id.replace(/[.@]/g, '_')).attr('class', class_status);
                                    $('#' + res.data.full_user_id.replace(/[.@]/g, '_')).find('i.status').text(res.data.presence_text);
                                } else {
                                    console.log(res.message);
                                }
                            },
                            error: function() {
                                console.log('Error occur when getPresenceStatus');
                            }
                        });
                    } else {
                        $('#presence_' + id_attr).text('Offline');
                    }
                }
            }, function () {
                console.log("Error kandy_refresh_chat");
            }
        );
    }
    setTimeout(kandy_refresh_chat, 10000);
};

/**
 * Send a message with kandyChat
 */
kandy_sendIm = function (username, dataHolder) {
    var displayName = jQuery('.kandyChat .kandy_current_username').val();
    var dataHolder = (typeof dataHolder!= 'undefined')? dataHolder : username;
    var inputMessage = jQuery('.kandyChat .imMessageToSend[data-user="' + dataHolder + '"]');    var message = inputMessage.val();
    inputMessage.val('');
    kandy.messaging.sendIm(username, message, function () {
                var newMessage = '<div class="my-message">\
                    <b><span class="imUsername">' + displayName + ':</span></b>\
                    <span class="imMessage">' + message + '</span>\
                </div>';
            var messageDiv = jQuery('.kandyChat .kandyMessages[data-user="' + dataHolder + '"]');
                messageDiv.append(newMessage);
                messageDiv.scrollTop(messageDiv[0].scrollHeight);
            },
            function () {
                alert("IM send failed");
            }
    );
};


/* Tab */

/**
 * Empty all contacts
 *
 */
var emptyContact = function () {
    jQuery(liTabContactWrap).html("");
    //jQuery(liContentWrapSelector).html("");
};

/**
 * Prepend a contact
 *
 * @param user
 */
var prependContact = function (user) {
    var isLiveChat = false;
    var username = user.contact_user_name;
    if(typeof user.user_email != "undefined"){
        isLiveChat = true;
        username = user.user_email;
    }

    var liParent = jQuery(liTabContactWrap + " li a[" + userHoldingAttribute + "='" + username + "']").parent();
    var liContact = "";
    if(liParent.length){
        liContact =  liParent[0].outerHTML;
    } else {
        liContact = getLiContact(user);
    }
    if(!isLiveChat){
        jQuery(liTabContactWrap).prepend(liContact);
    }else {
        jQuery(liTabLiveChatWrap).prepend(liContact);
        if(jQuery(liveChatGroupSeparator).hasClass('hide')){
            jQuery(liveChatGroupSeparator).removeClass('hide');
        }
    }
    if (!jQuery(liContentWrapSelector + " li[" + userHoldingAttribute + "='" + username + "']").length) {
        var liContent = getLiContent(username, user.contact_user_name);
        jQuery(liContentWrapSelector).prepend(liContent);
    }
};

/**
 * Get current active user name
 *
 * @returns {*}
 */
var getActiveContact = function () {
    return jQuery(liTabWrapSelector + " li." + activeClass).attr(userHoldingAttribute);
};

/**
 * Set focus to a user
 *
 * @param user
 */
var setFocusContact = function (user) {
    var username = user.contact_user_name;
    if(typeof user.user_email != "undefined"){
        username = user.user_email;
    }
    jQuery(liTabWrapSelector + " li a[" + userHoldingAttribute + "='" + username + "']").trigger("click");
};

/**
 * Move a contact user to top of the list
 *
 * @param user
 */
var moveContactToTop = function (user) {
    var username = user.contact_user_name;
    if(typeof user.user_email != "undefined"){
        username = user.user_email;
    }
    var contact = jQuery(liTabWrapSelector + " li a[" + userHoldingAttribute + "='" + username + "']").parent();
    var active = contact.hasClass(activeClass);

    // Add to top
    prependContact(user, active);
    // Remove
    contact.remove();

};

/**
 * Move a contact user to top of the list set set focus to it
 *
 * @param user
 */
var moveContactToTopAndSetActive = function (user) {
    moveContactToTop(user);
    setFocusContact(user);
    jQuery(liTabWrapSelector).scrollTop(0);
};

/**
 * Get a contact template
 *
 * @param user
 * @param active
 * @returns {string}
 */
var getLiContact = function (user, active) {
    // Set false as default
    var username = user.contact_user_name;
    var real_id = '';
    if(typeof user.user_email != 'undefined'){
        username = user.user_email;
        real_id = "data-real-id='" + user.contact_user_name + "' ";
    }
    var displayName = user.display_name;
    var id = username.replace(/[.@]/g, '_');
    var liClass = (typeof active !== 'undefined') ? active : "";
    return '<li id="' + id + '" class="' + liClass + '"><a ' + real_id + userHoldingAttribute + '="' + username + '" href="#">' + displayName + '</a><i class="status"></i></li>';
};

/**
 * Get contact content template
 *
 * @param user
 * @returns {string}
 */
var getLiContent = function (user, real_id) {
    var uid= '';
    if(typeof real_id != "undefined"){
        uid = real_id;
    }
    var result =
            '<li ' + userHoldingAttribute + '="' + user + '">\
                <div class="kandyMessages" data-user="' + user + '">\
                </div>\
                <div >\
                    Messages:\
                </div>\
                <div class="{{ $options["message"]["class"] }}">\
                            <form class="send-message" data-real-id="'+ uid + '" data-user="' + user + '">\
                        <div class="input-message">\
                            <input class="imMessageToSend chat-input" type="text" data-user="' + user + '">\
                            <div class="send-file">\
                                <label for="send-file">\
                                    <span class="icon-file"></span>\
                                </label>\
                                <input id="send-file" type="file" />\
                            </div>\
                        </div>\
                        <div class="button-send">\
                            <input class="btnSendMessage chat-input" type="submit" value="Send" data-user="' + user + '" >\
                        </div>\
                    </form>\
                </div>\
            </li>';
    return result;
};

/**
 * Filter contact
 *
 * @param val
 */
var kandy_contactFilterChanged = function (val) {
    var liUserchat = jQuery(".kandyChat .cd-tabs-navigation li");
    jQuery.each(liUserchat, function (index, target) {
        var liClass = jQuery(target).attr('class');
        var currentClass = "kandy-chat-status-" + val;
        var currentGroupClass = "kandy-chat-status-g-" +val;
        if (val == "all") {
            jQuery(target).show();
        }
        else if (currentClass == liClass || jQuery(target).hasClass(currentGroupClass)) {
            jQuery(target).show();
        }
        else {
            jQuery(target).hide();
        }
    });
};
/**
 * Add contact
 *
 */
var addContacts = function() {
    var contactId = jQuery("#kandySearchUserName").val();
    kandy_addToContacts(contactId);
    $(".select2").select2('val', '');
};


var kandy_getSessionInfo = function(sessionId, successCallback, failCallback){
    KandyAPI.Session.getInfoById(sessionId,
        function(result){
            if(typeof successCallback == 'function'){
                successCallback(result);
            }
        },
        function (msg, code) {
            if(typeof failCallback == 'function' ){
                failCallback(msg,code);
            }
        }
    )
};


/**
 * Load group details
 * @param sessionId
 */

var kandy_loadGroupDetails = function(groupId){
    kandy.messaging.getGroupById(groupId,
        function (result) {
            var isOwner = false, notInGroup = true, groupActivity = '', currentUser = jQuery(".kandy_user").val();
            var groupAction = jQuery(liTabWrapSelector +' li a[data-content="'+groupId+'"]').parent().find('.groupAction');
            var messageInput = jQuery(liContentWrapSelector + ' li[data-content="'+groupId+'"] form .imMessageToSend');
            buildListParticipants(groupId, result.members, result.owners[0].full_user_id);
            //if current user is owner of this group
            if(currentUser === result.owners[0].full_user_id ){
                //add admin functionality
                isOwner = true;
                groupActivity = '<a class="" href="javascipt:;"><i title="Remove group" onclick="kandy_terminateGroup(\''+result.group_id+'\')" class="fa fa-remove"></i></a>';
                jQuery(liTabWrapSelector + ' li[data-group="'+groupId+'"] ' + ' .'+ listUserClass+' li[data-user!="'+result.owners[0].full_user_id +'"] .actions').append(
                    '<i title="Remove user" class="remove fa fa-remove"></i>'
                );
            }
            //check if user is not in group
            for(var j in result.members){
                if(result.members[j].full_user_id == currentUser){
                    notInGroup = false;
                }
            }
            if(isOwner){
                groupActivity += '<a class="btnInviteUser" title="Add user" data-reveal-id="inviteModal"  href="javascript:;"><i class="fa fa-plus"></i></a>';
                //disable message input if user not belongs to a specific group
            }else {
                groupActivity = '<a class="leave" title="Leave group" onclick="kandy_leaveGroup(\''+result.group_id+'\',kandy_loadGroupDetails)" href="javascript:;"><i class="fa fa-sign-out"></i></a>';
                if(messageInput.is(':disabled')){
                    messageInput.prop('disabled',false);
                    jQuery('.toggle').trigger('click');
                }
            }
            groupAction.html(groupActivity);

            updateUserGroupStatus();
        },
        function (msg, code) {
            console.log('Error: '+ code + ' - ' + msg);
        }
    );

};

/**
 * Build list of participants
 * @param sessionDetails
 */

var buildListParticipants = function(sessionId, participants, admin_id){
    var listUsersGroup = jQuery(liTabWrapSelector + ' li[data-group="'+sessionId+'"] ' + ' .'+ listUserClass);
    listUsersGroup.empty();
    participants.push({full_user_id : admin_id});
    participants = getDisplayNameForContact(participants);
    var currentUser = jQuery(".kandy_user").val();
    if(participants.length){
        for(var i in participants) {
            if(!displayNames.hasOwnProperty(participants[i].full_user_id)){
                displayNames[participants[i].full_user_id] = participants[i].display_name;
            }
            if(!jQuery(listUsersGroup).find('li[data-user="'+participants[i].full_user_id+'"]').length) {
                var status = '';
                var additionBtn = '';
                var displayName = displayNames[participants[i].full_user_id];
                if(admin_id == participants[i].full_user_id){
                    displayName += '<span> (owner)</span>';
                }
                jQuery(listUsersGroup).append(
                    '<li data-user="'+participants[i].full_user_id+'">' +
                    '<a>'+ displayName +'</a>'+
                    '<span class="actions">'+additionBtn +'</span>'+
                    '<i class="status">'+status+'</i>'+
                    '</li>'
                );
            }

        }
    }

};
/**
 * Load open group chat
 */
var kandy_loadGroups = function(){
    kandy.messaging.getGroups(
        function (result) {
            jQuery(liTabGroupsWrap).empty();
            if(result.hasOwnProperty('groups')){
                if(result.groups.length){
                    jQuery(groupSeparator).removeClass('hide');
                    for(var i in result.groups){
                        //build sessions list here
                        groupNames[result.groups[i].group_id] = result.groups[i].group_name;
                        if (!jQuery(liTabGroupsWrap + " li[data-group='" + result.groups[i].group_id + "']").length){
                            jQuery(liTabGroupsWrap).append(
                                '<li data-group="'+result.groups[i].group_id+'" class="group">'+
                                '<i class="toggle fa fa-plus-square-o"></i>'+
                                '<a data-content="'+ result.groups[i].group_id+'" href="#">'+
                                result.groups[i].group_name+
                                '</a>'+
                                '<div class="groupAction"></div>'+
                                '<ul class="list-users"></ul>'+
                                '</li>'
                            );
                        }
                        if (!jQuery(liContentWrapSelector + " li[" + userHoldingAttribute + "='" + result.groups[i].group_id + "']").length) {
                            var liContent = getGroupContent(result.groups[i].group_id);
                            jQuery(liContentWrapSelector).prepend(liContent);

                        }
                        kandy_loadGroupDetails(result.groups[i].group_id);
                    }
                }else{
                    jQuery(groupSeparator).addClass('hide');
                }
            }
        },
        function (msg, code) {
            console.debug('load sessions fail. Code:'+ code +'. Message:'+msg);
        }
    );
};

/**
 *  Event handler for onData event
 */
var kandy_onGroupMessage = function(msg){
    if(typeof msg != 'undefined'){
        var msgType = msg.messageType;
        var sender = displayNames[msg.sender.full_user_id] || msg.sender.user_id;
        if(msgType == 'groupChat'){
            if(msg.contentType == 'text'){
                var newMessage = '<div class="their-message">\
                            <b><span class="imUsername">' + sender + ':</span></b>\
                            <span class="imMessage">' + msg.message.text + '</span>\
                        </div>';

                var messageDiv = jQuery('.kandyChat .kandyMessages[data-group="'+msg.group_id+'"]');
                messageDiv.append(newMessage);
                messageDiv.scrollTop(messageDiv[0].scrollHeight);
            }

        }
    }

};
/**
 * on Message event listener callback
 * @param msg
 */
var kandy_onMessage = function(msg) {
    if(msg){
        msg = getDisplayNameForChatContent(msg);
    }
    if(msg.messageType == 'chat'){
        // Get user info
        var username = msg.sender.full_user_id;
        if(typeof msg.sender.user_email != "undefined" ){
            username = msg.sender.user_email;
        }
        var displayName = msg.sender.display_name;
        // Process tabs
        if (!jQuery(liTabWrapSelector + " li a[" + userHoldingAttribute + "='" + username + "']").length) {
            prependContact(msg.sender);
        }
        if (!jQuery('input.imMessageToSend').is(':focus')) {
            moveContactToTopAndSetActive(msg.sender);
        } else {
            moveContactToTop(msg.sender);
        }
        // Process message
        if ((msg.hasOwnProperty('message'))) {
            var message = msg.message.text;
            var newMessage = '<div class="their-message">\
                            <b><span class="imUsername">' + displayName + ': </span></b>';

            if (msg.contentType === 'text' && msg.message.mimeType == 'text/plain') {
                newMessage += '<span class="imMessage">' + message + '</span>';
            } else {
                var fileUrl = kandy.messaging.buildFileUrl(msg.message.content_uuid);
                var html = '';
                if (msg.contentType == 'image') {
                    html = '<img src="' + fileUrl + '">';
                }
                html += '<a class="icon-download" href="' + fileUrl + '" target="_blank">' + msg.message.content_name + '</a>';
                newMessage += '<span class="imMessage">' + html + '</span>';
            }

            newMessage += '</div>';

            var messageDiv = jQuery('.kandyChat .kandyMessages[data-user="' + username + '"]');
            messageDiv.append(newMessage);
            messageDiv.scrollTop(messageDiv[0].scrollHeight);
        }
    }

};

// Gather the user input then send the image.
send_file = function () {
    // Gather user input.
    var recipient = jQuery(".livechats a.selected").data('real-id');
    if (typeof recipient == "undefined") {
        recipient = jQuery(".contacts a.selected").data('content');
        if (typeof recipient == "undefined") {
            recipient = jQuery(".cd-tabs-content form.send-message").data('real-id');
        }
    }
    var file = jQuery("#send-file")[0].files[0];

    if (file.type.indexOf('image') >=0) {
        kandy.messaging.sendImWithImage(recipient, file, onFileSendSuccess, onFileSendFailure);
    } else if (file.type.indexOf('audio') >=0) {
        kandy.messaging.sendImWithAudio(recipient, file, onFileSendSuccess, onFileSendFailure);
    } else if (file.type.indexOf('video') >=0) {
        kandy.messaging.sendImWithVideo(recipient, file, onFileSendSuccess, onFileSendFailure);
    } else if (file.type.indexOf('vcard') >=0) {
        kandy.messaging.sendImWithContact(recipient, file, onFileSendSuccess, onFileSendFailure);
    } else {
        kandy.messaging.sendImWithFile(recipient, file, onFileSendSuccess, onFileSendFailure);
    }
};

// What to do on a file send success.
function onFileSendSuccess(message) {
    console.log(message.message.content_name + " sent successfully.");
    var displayName = jQuery('.kandyChat .kandy_current_username').val();
    var dataHolder = jQuery('.cd-tabs-content > li.selected').data('content');
    var newMessage = '<div class="my-message">\
                    <b><span class="imUsername">' + displayName + ': </span></b>';


    var fileUrl = kandy.messaging.buildFileUrl(message.message.content_uuid);
    var html = '';
    if (message.contentType == 'image') {
        html = '<img src="' + fileUrl + '">';
    }
    html += '<a class="icon-download" href="' + fileUrl + '" target="_blank">' + message.message.content_name + '</a>';
    newMessage += '<span class="imMessage">' + html + '</span>';
    newMessage += '</div>';

    var messageDiv = jQuery('.kandyChat .kandyMessages[data-user="' + dataHolder + '"]');
    messageDiv.append(newMessage);
    messageDiv.scrollTop(messageDiv[0].scrollHeight);
}

// What to do on a file send failure.
function onFileSendFailure() {
    console.log("File send failure.");
}

/**
 * Add member to a group
 * @param group_id
 * @param members
 */
var kandy_inviteUserToGroup = function(group_id, members){
    kandy.messaging.addGroupMembers(group_id, members,

        function(results) {
            kandy_loadGroupDetails(group_id);
        },
        function(msg, code) {
            alert('Error - something went wrong when we tried to addGroupMembers');
        }

    );
};
/**
 * on group invite user event
 */
var kandy_onGroupInvite = function() {
      kandy_loadGroups();
};

var getGroupContent = function (groupId) {
    var result =
        '<li ' + userHoldingAttribute + '="' + groupId + '">\
                <div class="kandyMessages" data-group="' + groupId + '">\
                </div>\
                <div >\
                    Messages:\
                </div>\
                <div class="">\
                            <form class="send-message" data-group="' + groupId + '">\
                        <div class="input-message">\
                            <input class="imMessageToSend chat-input" type="text" data-group="' + groupId + '">\
                        </div>\
                        <div class="button-send">\
                            <input class="btnSendMessage chat-input" type="submit" value="Send"  data-group="' + groupId + '" >\
                        </div>\
                    </form>\
                </div>\
            </li>';
    return result;
};
var kandy_createSession = function(config, successCallback, failCallback) {
    KandyAPI.Session.create(
        config,
        function(result){
            if(typeof successCallback == "function"){
                activateSession(result.session_id);
                successCallback(result);
            }
        },
        function(){
            if(typeof failCallback == "function"){
                failCallback();
            }
        }
    )
};

var changeGroupInputState = function(groupId, state) {
    var messageInput = jQuery(liContentWrapSelector + ' li[data-content="'+groupId+'"] form .imMessageToSend');
    messageInput.prop('disabled',!!state);
    if(!!state == false) {
        jQuery('.toggle').trigger('click');
    }
};

var kandy_createGroup = function(groupName, successCallback, failCallback){
    kandy.messaging.createGroup(groupName, "", successCallback, failCallback);
};
/**
 * Send group IM
 * @param groupId
 * @param msg
 */
var kandy_sendGroupIm = function(groupId,msg){
    var username = jQuery("input.kandy_current_username").val();
    kandy.messaging.sendGroupIm(groupId, msg,
        function() {
            var newMessage = '<div class="my-message">\
                    <b><span class="imUsername">' + username + ':</span></b>\
                    <span class="imMessage">' + msg + '</span>\
                </div>';
            var messageDiv = jQuery('.kandyChat .kandyMessages[data-group="' + groupId + '"]');
            messageDiv.append(newMessage);
            messageDiv.scrollTop(messageDiv[0].scrollHeight);
        },
        function(msg, code) {
            console.log('Error sending Data (' + code + '): ' + msg);
        }
    );
};

/**
 * onJoinApprove event use for co-browsing session
 * @param notification
 */
var kandy_onSessionJoinApprove = function(notification){
    if(typeof sessionJoinApprovedCallback !== 'undefined'){
        sessionJoinApprovedCallback(notification.session_id);
    }
};

/**
 * Approve join session request
 * @param sessionId
 * @param userId
 * @param successCallback
 */
var kandy_ApproveJoinSession = function(sessionId, userId, successCallback){
    KandyAPI.Session.acceptJoinRequest(sessionId, userId,
        function () {
            if(typeof successCallback == "function"){
                successCallback(sessionId);
            }
        },
        function (msg, code) {
            console.log('Error:'+code+': '+msg);
        }
    );
};


/**
 *
 * @param notification
 */
var kandy_onLeaveGroup = function(message){
    var leaverDisplayName =  displayNames[message.leaver] || message.split('@')[0];
    var groupId = message.group_id;
    var LoggedUser = jQuery(".kandy_user").val();
    var notify = leaverDisplayName + ' is left';
    if (message.leaver != LoggedUser){
        kandy_loadGroupDetails(message.group_id);
    } else {
        kandy_loadGroups();
        changeGroupInputState(message.group_id, true);
    }
    var newMessage = '<div class="their-message">\
                    <span class="imMessage"><i>' +notify+ '</i></span>\
                </div>';
    var messageDiv = jQuery('.kandyChat .kandyMessages[data-group="' + groupId + '"]');
    messageDiv.append(newMessage);
};
/**
 * user removed from group chat event
 * @param message
 */
var kandy_onRemovedFromGroup = function(message){
    var bootedUser = message.booted[0];
    var notify;
    if(bootedUser != jQuery('.kandy_user').val()){
        notify = bootedUser.split('@')[0] + ' is removed from this group';
        kandy_loadGroupDetails(message.group_id);
    }else {
        notify = 'You are removed from this group';
        kandy_loadGroups();
        changeGroupInputState(message.group_id, true);
    }
    var newMessage = '<div class="their-message">\
                    <span class="imMessage"><i>' +notify+ '</i></span>\
                </div>';
    var messageDiv = jQuery('.kandyChat .kandyMessages[data-group="' + message.group_id + '"]');
    messageDiv.append(newMessage);
};

/**
 * Remove user from group
 * @param sessionId
 * @param userId
 */
var kandy_removeFromGroup = function(groupId, userId) {
    var members = [];
    members.push(userId);
    var displayName = displayNames[userId] || userId.split('@')[0];
    var confirm = window.confirm("Do you want to remove "+displayName +' from this group?');
    if(confirm){
        kandy.messaging.removeGroupMembers(groupId, members,
            function () {
                kandy_loadGroupDetails(groupId);
            },
            function (msg, code) {
                console.log(code + ': ' + msg);
            }
        );
        $('li[data-user="' + userId + '"]').remove();
    }
};

var activateSession = function(groupId){
    KandyAPI.Session.activate(
        groupId,
        function(){
            //success callback
            console.log('activate group successful');
        },function(){
            //fail callback
            console.log('Error activating group');
        }
    );

};

var kandy_joinSession = function (sessionId, successCallback){
    KandyAPI.Session.join(
        sessionId,
        {},
        function () {
            if(typeof successCallback == "function"){
                successCallback(sessionId);
            }
        },
        function (msg, code) {
            console.log(code + ": " + msg);
        }
    );
};

var kandy_LeaveSession= function(sessionId, successCallBack){
    KandyAPI.Session.leave(sessionId,
        '',
        function(){
            if(typeof successCallBack == 'function'){
                successCallBack(sessionId);
            }
        },
        function(){
            console.log('Leave group fail');
        }
    )
};
var kandy_leaveGroup = function(groupId, successCallback, failCallback){
    var confirm = window.confirm("Do you want to leave group "+ groupNames[groupId]);
    if(confirm){
        kandy.messaging.leaveGroup(groupId, successCallback, failCallback);
        $('li[data-group="' + groupId + '"]').remove();
    }
};

var kandy_onJoin = function(notification){
    kandy_loadGroupDetails(notification.session_id);
};

/**
 * Terminate a session
 * @param sessionId
 */
var kandy_terminateSession = function(sessionId, successCallback){
    KandyAPI.Session.terminate(
        sessionId,
        function(){
            if(typeof successCallback == "function"){
                successCallback();
            }
        },
        function (msg, code) {
            console.log('Terminate session fail : '+code+': '+msg);
        }
    );
};

var kandy_terminateGroup = function(groupId, successCallback, failCallback){
    var confirm = window.confirm("Do you want to remove this group?");
    if(confirm){
        kandy.messaging.deleteGroup(groupId, successCallback, failCallback);
        $('li[data-group="' + groupId + '"]').remove();
    }

};

/**
 * session terminate event callback
 * @param notification
 */
var kandy_onTerminateGroup = function(notification){
    removeGroupContent(notification.session_id);
    kandy_loadGroups();
};
/**
 * session active event callback
 * @param notification
 */
var kandy_onActiveGroup = function(notification){
    kandy_loadGroups();
};
/**
 * Clean things up after remove group
 * @param sessionId
 */
var removeGroupContent = function(sessionId){
    var toBeRemove = jQuery(liContentWrapSelector + ' li[data-content="'+sessionId+'"]');
    if(toBeRemove.hasClass('selected')){
        toBeRemove.siblings('[data-content="example"]').addClass('selected');
    }
    toBeRemove.remove();
};

var updateUserGroupStatus = function (){
    if(usersStatus){
        if(jQuery(liTabGroupsWrap).length){
            for(var u in usersStatus){
                var liUserGroup = jQuery(liTabGroupsWrap + ' li[data-user="'+u+'"]');
                var status = usersStatus[u].replace(/ /g,'-').toLowerCase();
                liUserGroup.find('i.status').html(usersStatus[u]);
                liUserGroup.removeClass();
                liUserGroup.addClass('kandy-chat-status-' + status );
                liUserGroup.attr('title', usersStatus[u]);
                jQuery(liUserGroup).closest("li[data-group]").addClass('kandy-chat-status-g-'+status);
            }

        }
    }
};


var kandy_make_pstn_call = function (target){
    var kandyButtonId = jQuery(target).data('container');
    activeContainerId = kandyButtonId;
    kandy.call.makePSTNCall(jQuery('#'+kandyButtonId+' #psntCallOutNumber').val(), 'demo');
    if(typeof kandy_pstn_callback == "function"){
        kandy_pstn_callback();
    }

    changeAnswerButtonState("CALLING", '#'+ kandyButtonId);
};

var kandy_getOpenSessionsByType = function(sessionType, successCallback){
    KandyAPI.Session.getOpenSessionsByType (
        sessionType,
        function(result){
            if(typeof successCallback == "function") {
                successCallback(result.sessions);
            }
        },
        function(msg, code){

        }
    );
};
var getCoBrowsingSessions = function() {
    kandy_getOpenSessionsByType('cobrowsing', loadSessionList);
};

var kandy_startCoBrowsing = function(sessionId) {
    KandyAPI.CoBrowse.startBrowsingUser(sessionId);
};

var kandy_stopCoBrowsing = function() {
    KandyAPI.CoBrowse.stopBrowsingUser();
};
/**
 * @param sessionId
 * @param holder - id of browsing holder
 */
var kandy_startCoBrowsingAgent = function(sessionId, holder) {
    KandyAPI.CoBrowse.startBrowsingAgent(sessionId, holder);
};

var kandy_stopCoBrowsingAgent = function() {
    KandyAPI.CoBrowse.stopBrowsingAgent();
};
/**
 * on join request callback, currently use for co-browser
 * @param notification
 */
var kandy_onSessionJoinRequest = function(notification) {
    var message = 'User '+notification.full_user_id+' request to join session '+ notification.session_id;
    var confirm = window.confirm(message);
    if(confirm){
        kandy_ApproveJoinSession(notification.session_id, notification.full_user_id);
    }else{
        console.log("join request has been disapprove");
    }
};

var kandy_sendSms = function(receiver, sender, message, successCallback, errorCallback) {
    kandy.call.sendSMS(
        receiver,
        sender,
        message,
        function() {
            if(typeof successCallback == 'function'){
                successCallback();
            }
        },
        function(message, status) {
            if(typeof errorCallback == 'function'){
                errorCallback(message, status);
            }
        }
    );
};

var kandy_updateUserStatus = function() {
    $.ajax({
        url: baseUrl + '/kandy/updateUserStatus',
        async: false
    });
};

var heartBeat = function(interval){
    return setInterval(function(){
        $.get('/kandy/stillAlive');
    },parseInt(interval));
};


// ======================JQUERY READY =======================
$(document).ready(function () {
    $.noConflict();
    setup();
    if (typeof login == 'function') {
        console.log('login....');
        login();
    }

    if ($(".kandyChat").length) {
        $(document).on('change', ".kandyChat input[type=file]", function (e){
            var fileName = $(this).val();
            if (fileName != '') {
                send_file();
            }
        });
    }

    //update that user is login for chat right now
    $(".select2").select2({
        ajax: {
            quietMillis: 100,
            url: baseUrl + "/kandy/getUsersForSearch",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params,
                    _token: _token
                };
            },
            results: function (data) {
                return {results: data.results};
            }
        },
        minimumInputLength: 1
    });

    $(document).on('click', ".btnInviteUser", function(e){
        $("#inviteModal").attr('data-group', $(this).closest('li.group').data('group'));
        $('a.close-reveal-modal').trigger('click');
    });

    $(document).on('click', 'a[data-reveal-id="inviteModal"]', function(e){
        e.preventDefault();
        $('#inviteModal').foundation('reveal', 'open');
    });

    $(document).on('click', 'a[data-reveal-id="myModal"]', function(e){
        e.preventDefault();
        $('#myModal').foundation('reveal', 'open');
    });

    $(document).on('click', 'button[data-reveal-id="sessionModal"]', function(e){
        e.preventDefault();
        $('#sessionModal').foundation('reveal', 'open');
    });

    $(document).on('click', 'a.close-reveal-modal', function(e){
        e.preventDefault();
        $('#myModal').foundation('reveal', 'close');
        $('#inviteModal').foundation('reveal', 'close');
        $('#sessionModal').foundation('reveal', 'close');
    });

    //Full Screen Video Chat
    $("span.video").each(function (index, value) {
        $(this).on("DOMSubtreeModified", appendFullScreen);
    });

    function appendFullScreen(event) {
        if ($(event.target).find('video').length > 0 && $(event.target).find('.icon-full-screen').length == 0) {
            $(event.target).off( "DOMSubtreeModified" );
            $(event.target).append('<span class="icon-full-screen"></span>');
            $(event.target).on( "DOMSubtreeModified", appendFullScreen );
        }
    }

    $(document).on('click', "span.icon-full-screen", function (e) {
        launchIntoFullscreen($(this).prev('video')[0]);
    });

    // Find the right method, call on correct element
    function launchIntoFullscreen(element) {
        if(element.requestFullscreen) {
            element.requestFullscreen();
        } else if(element.mozRequestFullScreen) {
            element.mozRequestFullScreen();
        } else if(element.webkitRequestFullscreen) {
            element.webkitRequestFullscreen();
        } else if(element.msRequestFullscreen) {
            element.msRequestFullscreen();
        }
    }
    //End Full Screen Video Call
});
