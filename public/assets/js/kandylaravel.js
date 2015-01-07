//========================KANDY SETUP AND LISTENER CALLBACK ==============

setup = function () {
    // initialize KandyAPI.Phone, passing a config JSON object that contains listeners (event callbacks)
    KandyAPI.Phone.setup({
        allowAutoLogin: true,
        // respond to Kandy events...
        listeners: {
            loginsuccess: kandy_loginsuccess_callback,
            loginfailed: kandy_loginfailed_callback,
            callincoming: kandy_incoming_call_callback,
            // when an outgoing call is connected
            oncall: kandy_oncall_callback,
            // when an incoming call is connected
            // you indicated that you are answering the call
            callanswered: kandy_callanswered_callback,
            callended: kandy_callended_callback,
            localvideoinitialized: kandy_localvideoinitialized_callback,
            // a video tag is being provided (required for both audio and video calls)
            // you must insert it into the DOM for communication to happen (although for audio calls, it can remain hidden)
            remotevideoinitialized: kandy_remotevideoinitialized_callack,
            presencenotification: kandy_presencenotification_callack
        }
    });
}

/**
 * Login Success Callback
 */
kandy_loginsuccess_callback = function () {
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
    if (typeof loginsuccess_callback == 'function') {
        loginsuccess_callback();
    }

    //call user logout if exists
    if (typeof kandy_logout == 'function') {
        kandy_logout();
    }
}

/**
 * Login Fail Callback
 */
kandy_loginfailed_callback = function () {
    if (typeof loginfailed_callback == 'function') {
        loginfailed_callback();
    }
}

/**
 * Local Video Initialized callback
 * @param videoTag
 */
kandy_localvideoinitialized_callback = function (videoTag) {

    //have video widget
    if($(".kandyVideo").length){
        $('#myVideo').append(videoTag);
    }

    if (typeof localvideoinitialized_callback == 'function') {
        localvideoinitialized_callback(videoTag);
    }

}

/**
 * Remote Video Initialized Callback
 * @param videoTag
 */
kandy_remotevideoinitialized_callack = function (videoTag) {

    //have video widget
    if($(".kandyVideo").length){
        $('#theirVideo').append(videoTag);
    }
    //have voice call widget
    if($(".kandyButton .videoVoiceCallHolder").length){
        $('.kandyButton .videoVoiceCallHolder .video').append(videoTag);
    }
    if (typeof remotevideoinitialized_callack == 'function') {
        remotevideoinitialized_callack(videoTag);
    }
}

/**
 * Status Notification Callback
 * @param userId
 * @param state
 * @param description
 * @param activity
 */
kandy_presencenotification_callack = function (userId, state, description, activity) {
    // HTML id can't contain @ and jquery doesn't like periods (in id)
    var id_attrib = '.kandyAddressBook .kandyAddressContactList #presence_' + userId.replace(/[.@]/g, '_');
    $(id_attrib).text(description);
    if (typeof presencenotification_callack == 'function') {
        presencenotification_callack(userId, state, description, activity);
    }
}

/**
 * OnCall Callback
 * @param call
 */
kandy_oncall_callback = function (call) {
    if (typeof oncall_callback == 'function') {
        oncall_callback(call);
    }
    changeAnswerButtonState("ON_CALL");
}

/**
 * Incomming Callback
 * @param call
 * @param isAnonymous
 */
kandy_incoming_call_callback = function (call, isAnonymous) {
    if (typeof callincoming_callback == 'function') {
        callincoming_callback(call, isAnonymous);
    }
    changeAnswerButtonState('BEING_CALLED');
}

/**
 * kandy call answered callback
 * @param call
 * @param isAnonymous
 */
kandy_callanswered_callback = function (call, isAnonymous) {
    if (typeof callanswered_callback == 'function') {
        callanswered_callback(call, isAnonymous);
    }
    changeAnswerButtonState("ON_CALL");
}

/**
 * kandy callended callback
 */
kandy_callended_callback = function () {
    //have video widget
    if($(".kandyVideo").length){
        $('#theirVideo').empty();
        $('#myVideo').empty();
    }
    if (typeof callended_callback == 'function') {
        callended_callback();
    }
    changeAnswerButtonState("READY_FOR_CALLING");
}

/**
 * Change AnswerButtonState with KandyButton Widget
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
}

/*
 Event when answer a call
 */
kandy_answerVideoCall = function (target) {
    KandyAPI.Phone.answerVideoCall();
    changeAnswerButtonState("ANSWERING_CALL");
    if (typeof answerVideoCall_callback == 'function') {
        answerVideoCall_callback("ANSWERING_CALL");
    }
}

/*
 Event when click call button
 */
kandy_makeVideoCall = function (target) {

    KandyAPI.Phone.makeVideoCall($('.kandyButton .kandyVideoButtonCallOut #callOutUserId').val());
    changeAnswerButtonState("CALLING");
}

/*
 Event when answer a voice call
 */
kandy_answerVoiceCall = function (target) {
    KandyAPI.Phone.answerVoiceCall();
    changeAnswerButtonState("ANSWERING_CALL");
    answerVoiceCall_callback("ANSWERING_CALL");
    if (typeof answerVoiceCall_callback == 'function') {
        answerVoiceCall_callback("ANSWERING_CALL");
    }

}

/*
 Event when click call button
 */
kandy_makeVoiceCall = function (target) {

    KandyAPI.Phone.makeVoiceCall($('.kandyButton .kandyVideoButtonCallOut #callOutUserId').val());
    changeAnswerButtonState("CALLING");
}

/*
 Event when click end call button
 */
kandy_endCall = function (target) {
    KandyAPI.Phone.endCall();
    if (typeof endCall_callback == 'function') {
        endCall_callback('READY_FOR_CALLING');
    }

    changeAnswerButtonState("READY_FOR_CALLING");
}

/**
 * ADDRESS BOOK WIDGET
 */
/**
 * Load contact list for addressBook widget
 */
kandy_loadContacts_addressBook = function () {
    var contactListForPresence = [];
    KandyAPI.Phone.retrievePersonalAddressBook(
        function (results) {
            // clear out the current address book list
            $(".kandyAddressBook .kandyAddressContactList div:not(:first)").remove();
            var div = null;
            if (results.length == 0) {
                div = "<div class='kandyAddressBookNoResult'>-- No Contacts --</div>";
                $('.kandyAddressBook .kandyAddressContactList').append(div);
            } else {

                for (i = 0; i < results.length; i++) {
                    contactListForPresence.push({full_user_id: results[i].contact_user_name});

                    var id_attrib = results[i].contact_user_name.replace(/[.@]/g, '_');
                    $('.kandyAddressBook .kandyAddressContactList').append(
                        // HTML id can't contain @ and jquery doesn't like periods (in id)
                        "<div class='kandyContactItem' id='uid_" + results[i].contact_user_name.replace(/[.@]/g, '_') + "'>" +
                        "<span class='userid'>" + results[i].contact_user_name + "</span>" +
                        "<span id='presence_" + id_attrib + "' class='presence'></span>" +
                        "<input class='removeBtn' type='button' value='Remove' " +
                        " onclick='kandy_removeFromContacts(\"" + results[i].contact_id + "\")'>" +
                        "</div>"
                    );
                }
                KandyAPI.Phone.watchPresence(contactListForPresence);
            }
        },
        function () {
            console.log("Error kandy_loadContacts_addressBook");
        }
    );

}

/**
 * Change current user status with kandyAddressBook
 * @param status
 */
kandy_myStatusChanged = function (status) {
    KandyAPI.Phone.updatePresence(status);
}

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
                    if (results[i].primaryContact === userIdToAddToContacts) {
                        // user name and nickname are required
                        contact = {
                            contact_user_name: results[i].primaryContact,
                            contact_nickname: results[i].primaryContact
                        };
                        if (results[i].firstName) {
                            contact['contact_first_name'] = results[i].firstName;
                        }
                        if (results[i].lastName) {
                            contact['contact_last_name'] = results[i].lastName;
                        }
                        if (results[i].homePhone) {
                            contact['contact_home_phone'] = results[i].homePhone;
                        }
                        if (results[i].mobilePhone) {
                            contact['contact_mobile_number'] = results[i].mobilePhone;
                        }
                        if (results[i].workPhone) {
                            contact['contact_business_number'] = results[i].workPhone;
                        }
                        if (results[i].fax) {
                            contact['contact_fax'] = results[i].fax;
                        }
                        if (results[i].email) {
                            contact['contact_email'] = results[i].email;
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
    KandyAPI.Phone.searchDirectoryByUserName(
        userName,
        function (results) {
            // clear out the results, but not the first line (results title)
            $(".kandyAddressBook .kandyDirSearchResults div:not(:first)").remove();
            var div = null;
            if (results.length == 0) {
                div = "<div class='kandyAddressBookNoResult'>-- No Matches Found --</div>";
                $('.kandyAddressBook .kandyDirSearchResults').append(div);
            } else {
                for (var i = 0; i < results.length; i++) {
                    $('.kandyDirSearchResults').append(
                        "<div class='kandySearchItem'>" +
                        "<span class='userId'>" + results[i].primaryContact + "</span>" +
                        "<input type='button' value='Add Contact' onclick='kandy_addToContacts(\"" +
                        results[i].primaryContact + "\")' />" +
                        "</div>"
                    );
                }
            }
        },
        function (val) {
            console.log('Error kandy_searchDirectoryByUserName ');
        }
    );
};

/**
 * ===================KANDYCHAT WIDGET FUNCTION ==========================
 */

/**
 * Add an example chat box
 */
var addExampleBox = function () {
    var tabId = "example";
    tabContentWrapper.append(getLiContent(tabId));
    tabContentWrapper.find('li[data-content="' + tabId + '"]').addClass('selected').find(".chat-input").attr('disabled', true);
}

/**
 * Load Contact for KandyChat
 */
kandy_loadContacts_chat = function () {
    KandyAPI.Phone.retrievePersonalAddressBook(
        function (results) {
            var div = null;
            emptyContact();
            for (i = 0; i < results.length; i++) {
                prependContact(results[i].contact_user_name);
            }
            addExampleBox();
        },
        function () {
            console.log("Error");
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
    var uuid = KandyAPI.Phone.sendIm(username, message, function () {
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
            var i = 0;
            for (i = 0; i < data.messages.length; ++i) {
                var msg = data.messages[i];
                if (msg.messageType == 'chat') {
                    // Get user info
                    var username = data.messages[i].sender.full_user_id;
                    var shortName = data.messages[i].sender.user_id;

                    // Process tabs
                    if (!$(liTabWrapSelector + " li a[" + userHoldingAttribute + "='" + username + "']").length) {
                        prependContact(username);
                    }
                    if (!$('input.imMessageToSend').is(':focus')) {
                        moveContactToTopAndSetActive(username);
                    } else {
                        moveContactToTop(username);
                    }

                    // Process message
                    var msg = data.messages[i].message.text
                    var newMessage = '<div class="their-message">\
                            <b><span class="imUsername">' + shortName + ':</span></b>\
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
}

/**
 * Prepend a contact
 *
 * @param user
 */
var prependContact = function (user) {
    var liContact = getLiContact(user);
    $(liTabWrapSelector).prepend(liContact);
    if (!$(liContentWrapSelector + " li[" + userHoldingAttribute + "='" + user + "']").length) {
        var liContent = getLiContent(user);
        $(liContentWrapSelector).prepend(liContent);
    }
}

/**
 * Get current active user name
 *
 * @returns {*|jQuery}
 */
var getActiveContact = function () {
    var user = $(liTabWrapSelector + " li." + activeClass).attr(userHoldingAttribute);
    return user;
}

/**
 * Set focus to a user
 *
 * @param user
 */
var setFocusContact = function (user) {
    $(liTabWrapSelector + " li a[" + userHoldingAttribute + "='" + user + "']").trigger("click");
}

/**
 * Move a contact user to top of the list
 *
 * @param user
 */
var moveContactToTop = function (user) {
    var contact = $(liTabWrapSelector + " li a[" + userHoldingAttribute + "='" + user + "']").parent();
    var active = contact.hasClass(activeClass);
    // Remove
    contact.remove();
    // Add to top
    prependContact(user, active);

}

/**
 * Move a contact user to top of the list set set focus to it
 *
 * @param user
 */
var moveContactToTopAndSetActive = function (user) {
    moveContactToTop(user);
    setFocusContact(user);
    $(liTabWrapSelector).scrollTop(0);
}

// ======================JQUERY READY =======================
$(document).ready(function () {
    setup();
    login();
});