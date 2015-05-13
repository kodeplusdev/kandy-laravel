//========================KANDY SETUP AND LISTENER CALLBACK ==============

var unassignedUser = "KANDY UNASSIGNED USER";
var callId = null;

/**
 * Kandy Set up
 */
setup = function () {
    // initialize KandyAPI.Phone, passing a config JSON object that contains listeners (event callbacks)
    KandyAPI.Phone.setup({
        // respond to Kandy events...
        remoteVideoContainer: $('#theirVideo')[0],
        localVideoContainer: $('#myVideo')[0],
        listeners: {
            loginsuccess: kandy_login_success_callback,
            loginfailed: kandy_login_failed_callback,
            callinitiated: kandy_on_call_initiate_callback,
            callincoming: kandy_incoming_call_callback,
            oncall: kandy_on_call_callback,
            // when an incoming call is connected
            // you indicated that you are answering the call
            callanswered: kandy_call_answered_callback,
            callended: kandy_call_ended_callback,
            //callback when change presence status
            presencenotification: kandy_presence_notification_callback
            //callinitiated: kandy_local_video_initialized_callback
        }
    });
};

/**
 * Login Success Callback.
 */
kandy_login_success_callback = function () {
    KandyAPI.Phone.updatePresence(0);

    //have kandyAddressBook widget
    if ($(".kandyAddressBook").length) {
        kandy_loadContacts_addressBook();
    }
    //have kandyChat widget
    if ($(".kandyChat").length) {
        kandy_loadContacts_chat();
        setInterval(kandy_getIms, 3000);

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
    if (typeof login_failed_callback == 'function') {
        login_failed_callback();
    }
};

/**
 * Local Video Initialized callback
 * @param videoTag
 */
kandy_local_video_initialized_callback = function (videoTag) {
    //have video widget
    if($(".kandyVideo").length){
        $('#myVideo').append(videoTag);
    }

    if (typeof local_video_initialized_callback == 'function') {
        local_video_initialized_callback(videoTag);
    }

};

/**
 * Remote Video Initialized Callback
 * @param videoTag
 */
kandy_remote_video_initialized_callback = function (videoTag) {
    //have video widget
    if($(".kandyVideo").length){
        $('#theirVideo').append(videoTag);
    }
    //have voice call widget
    if($(".kandyButton .videoVoiceCallHolder").length){
        $('.kandyButton .videoVoiceCallHolder .video').append(videoTag);
    }
    if (typeof remote_video_initialized_callback == 'function') {
        remote_video_initialized_callback(videoTag);
    }
};

/**
 * Status Notification Callback.
 *
 * @param userId
 * @param state
 * @param description
 * @param activity
 */
kandy_presence_notification_callback = function (userId, state, description, activity) {
    // HTML id can't contain @ and jquery doesn't like periods (in id)
    var id_attr = '.kandyAddressBook .kandyAddressContactList #presence_' + userId.replace(/[.@]/g, '_');
    $(id_attr).text(description);
    if (typeof presence_notification_callback == 'function') {
        presence_notification_callback(userId, state, description, activity);
    }
    //update chat status
    if($('.kandyChat').length >0){
        var liUser = $('.kandyChat .cd-tabs-navigation li#' +userId.replace(/[.@]/g, '_'));
        var statusItem = liUser.find('i.status');
        statusItem.text(description);

        liUser.removeClass().addClass('kandy-chat-status-' + description.replace(/ /g,'-').toLowerCase());
        liUser.attr('title', description);
    }
};
/**
 * on call initiate callback
 * @param call
 */
kandy_on_call_initiate_callback = function(call){
    callId = call.getId();
}

/**
 * OnCall Callback
 * @param call
 */

kandy_on_call_callback = function (call) {
    if (typeof on_call_callback == 'function') {
        on_call_callback(call);
    }
    changeAnswerButtonState("ON_CALL");
};

/**
 * Incoming Callback.
 *
 * @param call
 * @param isAnonymous
 */
kandy_incoming_call_callback = function (call, isAnonymous) {
    callId = call.getId();
    if (typeof incoming_call_callback == 'function') {
        incoming_call_callback(call, isAnonymous);
    }
    changeAnswerButtonState('BEING_CALLED');
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
    changeAnswerButtonState("ON_CALL");
};

/**
 * Kandy call ended callback.
 *
 */
kandy_call_ended_callback = function () {
    //have video widget
    callId = null;
    if($(".kandyVideo").length){
        $('#theirVideo').empty();
        $('#myVideo').empty();
    }
    if (typeof call_ended_callback == 'function') {
        call_ended_callback();
    }
    changeAnswerButtonState("READY_FOR_CALLING");
};

/**
 * Change AnswerButtonState with KandyButton Widget.
 *
 * @param state
 */
changeAnswerButtonState = function (state) {
    switch (state) {
        case 'READY_FOR_CALLING':

            $('.kandyButton .kandyVideoButtonSomeonesCalling').hide();
            $('.kandyButton .kandyVideoButtonCallOut').show();
            $('.kandyButton .kandyVideoButtonCalling').hide();
            $('.kandyButton .kandyVideoButtonOnCall').hide();
            break;
        case 'BEING_CALLED':
            $('.kandyButton .kandyVideoButtonSomeonesCalling').show();
            $('.kandyButton .kandyVideoButtonCallOut').hide();
            $('.kandyButton .kandyVideoButtonCalling').hide();
            $('.kandyButton .kandyVideoButtonOnCall').hide();
            break;
        case 'CALLING':
            $('.kandyButton .kandyVideoButtonSomeonesCalling').hide();
            $('.kandyButton .kandyVideoButtonCallOut').hide();
            $('.kandyButton .kandyVideoButtonCalling').show();
            $('.kandyButton .kandyVideoButtonOnCall').hide();
            break;
        case 'ON_CALL':
            $('.kandyButton .kandyVideoButtonSomeonesCalling').hide();
            $('.kandyButton .kandyVideoButtonCallOut').hide();
            $('.kandyButton .kandyVideoButtonCalling').hide();
            $('.kandyButton .kandyVideoButtonOnCall').show();
            break;
    }
};

/**
 * Event when answer a call.
 *
 * @param target
 */
kandy_answer_video_call = function (target) {
    KandyAPI.Phone.answerCall(callId, true);
    changeAnswerButtonState("ANSWERING_CALL");
    if (typeof answer_video_call_callback == 'function') {
        answer_video_call_callback("ANSWERING_CALL");
    }
};

/*
 Event when click call button
 */
kandy_make_video_call = function (target) {
    KandyAPI.Phone.makeCall($('.kandyButton .kandyVideoButtonCallOut #callOutUserId').val(),true);
    changeAnswerButtonState("CALLING");
    if (typeof make_video_call_callback == 'function') {
        make_video_call_callback(target);
    }
};

/*
 Event when answer a voice call
 */
kandy_answer_voice_call = function (target) {
    KandyAPI.Phone.answerCall(callId, false);
    changeAnswerButtonState("ANSWERING_CALL");
    if (typeof answer_voice_call_callback == 'function') {
        answer_voice_call_callback(target);
    }

};

/*
 Event when click call button
 */
kandy_make_voice_call = function (target) {

    KandyAPI.Phone.makeCall($('.kandyButton .kandyVideoButtonCallOut #callOutUserId').val(),false);
    changeAnswerButtonState("CALLING");

    if (typeof make_voice_call_callback == 'function') {
        make_voice_call_callback(target);
    }
};

/*
 Event when click end call button
 */
kandy_end_call = function (target) {
    KandyAPI.Phone.endCall(callId);

    if (typeof end_call_callback == 'function') {
        end_call_callback(target);
    }

    changeAnswerButtonState("READY_FOR_CALLING");
};

/**
 * ADDRESS BOOK WIDGET
 */
/**
 * Load contact list for addressBook widget
 */
kandy_loadContacts_addressBook = function () {
    var contactListForPresence = [];
    var contactToRemove = [];
    KandyAPI.Phone.retrievePersonalAddressBook(
        function (results) {
            results = getDisplayNameForContact(results);
            // clear out the current address book list
            $(".kandyAddressBook .kandyAddressContactList div:not(:first)").remove();
            var div = null;
            if (results.length == 0) {
                div = "<div class='kandyAddressBookNoResult'>-- No Contacts --</div>";
                $('.kandyAddressBook .kandyAddressContactList').append(div);
            } else {
                $('.kandyAddressBook .kandyAddressContactList').append("<div class='kandy-contact-heading'><span class='displayname'><b>Username</b></span><span class='userid'><b>Contact</b></span><span class='presence_'><b>Status</b></span></div>");

                for (var i = 0; i < results.length; i++) {
                    var displayName = results[i].display_name;
                    var contactId = results[i].contact_id;

                    if (displayName == unassignedUser) {
                        contactToRemove.push(contactId);
                        continue;
                    }
                    contactListForPresence.push({full_user_id: results[i].contact_user_name});

                    var id_attr = results[i].contact_user_name.replace(/[.@]/g, '_');
                    $('.kandyAddressBook .kandyAddressContactList').append(
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
                KandyAPI.Phone.watchPresence(contactListForPresence);
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

/**
 * Change current user status with kandyAddressBook
 * 
 * @param status
 */
kandy_my_status_changed = function (status) {
    KandyAPI.Phone.updatePresence(status);
};

/**
 * Add a user to contact list with kandyAddressBook
 * @type {null}
 */
var userIdToAddToContacts = null;  // need access to this in anonymous function below
kandy_addToContacts = function (userId) {
    userIdToAddToContacts = userId;

    // HTML id can't contain @ and jquery doesn't like periods (in id)
    if ($('#uid_' + userId.replace(/[.@]/g, '_')).length > 0) {
        alert("This person is already in your contact list.")
    } else {
        // get and AddressBook.Entry object for this contact
        KandyAPI.Phone.searchDirectoryByUserName(
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

                        KandyAPI.Phone.addToPersonalAddressBook(
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
    KandyAPI.Phone.removeFromPersonalAddressBook(nickname,
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
    var userName = $('.kandyAddressBook .kandyDirectorySearch #kandySearchUserName').val();
    $.ajax({
        url: "/kandy/getUsersForSearch",
        data: {q:userName}
    }).done(function (results) {
            $(".kandyAddressBook .kandyDirSearchResults div:not(:first)").remove();
            var div = null;
            if (results.length == 0) {
                div = "<div class='kandyAddressBookNoResult'>-- No Matches Found --</div>";
                $('.kandyAddressBook .kandyDirSearchResults').append(div);
            } else {
                for (var i = 0; i < results.length; i++) {
                    $('.kandyDirSearchResults').append(
                        "<div class='kandySearchItem'>" +
                            "<span class='userId'>" + results[i].main_username + "</span>" +
                            "<input type='button' value='Add Contact' onclick='kandy_addToContacts(\"" +
                            results[i].kandy_full_username + "\")' />" +
                            "</div>"
                    );
                }
            }
        }).fail(function() {
            $(".kandyAddressBook .kandyDirSearchResults div:not(:first)").remove();
            var div = "<div class='kandyAddressBookNoResult'>There was an error with your request.</div>";
            $('.kandyAddressBook .kandyDirSearchResults').append(div);
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
var getDisplayNameForChatContent = function (data) {
    if (data.messages.length) {
        $.ajax({
            url: "/kandy/getNameForChatContent",
            data: {data:data.messages},
            async: false
        }).done(function(response) {
                data.messages = response;
            }).fail(function(e) {
            });
    }
    return data;
};

/**
 * Get display name for contacts
 *
 * @param data
 * @returns {*}
 */
var getDisplayNameForContact = function (data) {
    if (data.length) {
        $.ajax({
            url: "/kandy/getNameForContact",
            data: {data: data},
            async: false
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
    KandyAPI.Phone.retrievePersonalAddressBook(
        function (results) {
            results = getDisplayNameForContact(results);
            emptyContact();
            for (var i = 0; i < results.length; i++) {
                prependContact(results[i]);
                contactListForPresence.push({full_user_id: results[i].contact_user_name});
            }

            KandyAPI.Phone.watchPresence(contactListForPresence);
            addExampleBox();
        },
        function () {
            console.log("Error");
            addExampleBox();
        }
    );

};

/**
 * Send a message with kandyChat
 */
kandy_sendIm = function (username) {
    var displayName = $('.kandyChat .kandy_current_username').val();
    var inputMessage = $('.kandyChat .imMessageToSend[data-user="' + username + '"]');
    var message = inputMessage.val();
    inputMessage.val('');
    KandyAPI.Phone.sendIm(username, message, function () {
            var newMessage = '<div class="my-message">\
                    <b><span class="imUsername">' + displayName + ':</span></b>\
                    <span class="imMessage">' + message + '</span>\
                </div>';
            var messageDiv = $('.kandyChat .kandyMessages[data-user="' + username + '"]');
            messageDiv.append(newMessage);
            messageDiv.scrollTop(messageDiv[0].scrollHeight);
        },
        function () {
            alert("IM send failed");
        }
    );
};

/**
 * Get messages with kandyChat
 */
kandy_getIms = function () {
    KandyAPI.Phone.getIm(
        function (data) {
            if (data.messages.length) {
                data = getDisplayNameForChatContent(data);
            }

            var i;
            for (i = 0; i < data.messages.length; ++i) {
                var msg = data.messages[i];
                if (msg.messageType == 'chat') {
                    // Get user info
                    var username = data.messages[i].sender.full_user_id;
                    var displayName = data.messages[i].sender.display_name;

                    // Process tabs
                    if (!$(liTabWrapSelector + " li a[" + userHoldingAttribute + "='" + username + "']").length) {
                        prependContact(data.messages[i].sender);
                    }
                    if (!$('input.imMessageToSend').is(':focus')) {
                        moveContactToTopAndSetActive(data.messages[i].sender);
                    } else {
                        moveContactToTop(data.messages[i].sender);
                    }

                    // Process message
                    var msg = data.messages[i].message.text;
                    var newMessage = '<div class="their-message">\
                            <b><span class="imUsername">' + displayName + ':</span></b>\
                            <span class="imMessage">' + msg + '</span>\
                        </div>';

                    var messageDiv = $('.kandyChat .kandyMessages[data-user="' + username + '"]');
                    messageDiv.append(newMessage);
                    messageDiv.scrollTop(messageDiv[0].scrollHeight);
                } else {
                    //alert("received " + msg.messageType + ": ");
                }
            }
        },
        function () {
            console.log("error receiving IMs");
        }
    )
};

/* Tab */

/**
 * Empty all contacts
 *
 */
var emptyContact = function () {
    $(liTabWrapSelector).html("");
    $(liContentWrapSelector).html("");
};

/**
 * Prepend a contact
 *
 * @param user
 */
var prependContact = function (user) {

    var username = user.contact_user_name;

    var liParent = $(liTabWrapSelector + " li a[" + userHoldingAttribute + "='" + username + "']").parent();
    var liContact = "";
    if(liParent.length){
        liContact =  liParent[0].outerHTML;
    } else {
        liContact = getLiContact(user);
    }

    $(liTabWrapSelector).prepend(liContact);
    if (!$(liContentWrapSelector + " li[" + userHoldingAttribute + "='" + username + "']").length) {
        var liContent = getLiContent(username);
        $(liContentWrapSelector).prepend(liContent);
    }
};

/**
 * Get current active user name
 *
 * @returns {*}
 */
var getActiveContact = function () {
    return $(liTabWrapSelector + " li." + activeClass).attr(userHoldingAttribute);
};

/**
 * Set focus to a user
 *
 * @param user
 */
var setFocusContact = function (user) {
    $(liTabWrapSelector + " li a[" + userHoldingAttribute + "='" + user + "']").trigger("click");
};

/**
 * Move a contact user to top of the list
 *
 * @param user
 */
var moveContactToTop = function (user) {
    var username = user.contact_user_name;

    var contact = $(liTabWrapSelector + " li a[" + userHoldingAttribute + "='" + username + "']").parent();
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
    $(liTabWrapSelector).scrollTop(0);
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
    var displayName = user.display_name;
    var id = username.replace(/[.@]/g, '_');
    var liClass = (typeof active !== 'undefined') ? active : "";
    return '<li id="'+ id +'" class="' + liClass + '"><a ' + userHoldingAttribute + '="' + username + '" href="#">' + displayName + '</a><i class="status"></i></li>';
};

/**
 * Get contact content template
 *
 * @param user
 * @returns {string}
 */
var getLiContent = function (user) {
    var result =
        '<li ' + userHoldingAttribute + '="' + user + '">\
                <div class="kandyMessages" data-user="' + user + '">\
                </div>\
                <div >\
                    Messages:\
                </div>\
                <div class="{{ $options["message"]["class"] }}">\
                            <form class="send-message" data-user="' + user + '">\
                        <div class="input-message">\
                            <input class="imMessageToSend chat-input" type="text" data-user="' + user + '">\
                        </div>\
                        <div class="button-send">\
                            <input class="btnSendMessage chat-input" type="submit" value="Send"  data-user="' + user + '" >\
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
var kandy_contactFilterChanged = function(val){
    var liUserChat = $(".kandyChat .cd-tabs-navigation li");
    $.each(liUserChat, function(index, target){
        var liClass = $(target).attr('class');
        var currentClass = "kandy-chat-status-" + val;
        if(val == "all"){
            $(target).show();
        } else if(liClass == currentClass){
            $(target).show();
        } else {
            $(target).hide();
        }
    });
};

/**
 * Add contact
 *
 */
var addContacts = function() {
    var contactId = $("#kandySearchUserName").val();
    kandy_addToContacts(contactId);
    $("#kandySearchUserName").select2('val', '');
};

// ======================JQUERY READY =======================
$(document).ready(function () {
    setup();
    login();

    $(".select2").select2({
        ajax: {
            quietMillis: 100,
            url: "/kandy/getUsersForSearch",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params
                };
            },
            results: function (data) {
                return {results: data.results};
            }
        },
        minimumInputLength: 1
    });
});
