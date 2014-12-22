/*
 KANDY SETUP AND LISTENER CALLBACK
 */
setup = function () {
    // initialize KandyAPI.Phone, passing a config JSON object that contains listeners (event callbacks)
    KandyAPI.Phone.setup({
        // respond to Kandy events...
        listeners: {
            loginsuccess: kandy_loginsuccess_callback,
            loginfailed: kandy_loginfailed_callback,
            callincoming: kandy_incoming_call_callback,
            // when an outgoing call is connected
            oncall: oncall_callback,
            // when an incoming call is connected
            // you indicated that you are answering the call
            callanswered: kandy_callanswered_callback,
            callended: kandy_callended_callback,
            localvideoinitialized: localvideoinitialized_callback,
            // a video tag is being provided (required for both audio and video calls)
            // you must insert it into the DOM for communication to happen (although for audio calls, it can remain hidden)
            remotevideoinitialized: remotevideoinitialized_callack,
            presencenotification: function (userId, state, description, activity) {
                // HTML id can't contain @ and jquery doesn't like periods (in id)
                var id_attrib = '.kandyAddressBook .kandyAddressContactList #presence_' + userId.replace(/[.@]/g,'_');
                $(id_attrib).text(description);

            }
        }
    });
}
kandy_loginsuccess_callback = function(){
    KandyAPI.Phone.updatePresence(0);
    kandy_loadContacts();
    loginsuccess_callback();
}
kandy_loginfailed_callback = function(){
    loginfailed_callback();
}

kandy_incoming_call_callback = function (call, isAnonymous) {
    callincoming_callback(call, isAnonymous);
    changeAnswerButtonState('BEING_CALLED');
}
kandy_callanswered_callback = function (call, isAnonymous) {
    callanswered_callback(call, isAnonymous);
    changeAnswerButtonState("ON_CALL");
}
kandy_callended_callback = function(){
    callended_callback();
    changeAnswerButtonState("READY_FOR_CALLING");
}
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
    changeAnswerButtonState("ANSWERING_CALL");
    answerVideoCall_callback("ANSWERING_CALL");
    KandyAPI.Phone.answerVideoCall();
}
/*
 Event when click call button
 */
kandy_makeCall = function (target) {

    KandyAPI.Phone.makeVideoCall($('.kandyButton .kandyVideoButtonCallOut #callOutUserId').val());
    changeAnswerButtonState("CALLING");
}

/*
 Event when answer a voice call
 */
kandy_answerVoiceCall = function (target) {
    changeAnswerButtonState("ANSWERING_CALL");
    answerVideoCall_callback("ANSWERING_CALL");
    KandyAPI.Phone.answerVoiceCall();
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
    endCall_callback('READY_FOR_CALLING');
    changeAnswerButtonState("READY_FOR_CALLING");
}

kandy_incomingCall = function(){

}

//presence

kandy_loadContacts = function() {
    var contactListForPresence = [];
    KandyAPI.Phone.retrievePersonalAddressBook(
        function(results) {
            // clear out the current address book list
            $(".kandyAddressBook .kandyAddressContactList div:not(:first)").remove();
            var div = null;
            if (results.length == 0) {
                div = "<div class='kandyAddressBookNoResult'>-- No Contacts --</div>";
                $('.kandyAddressBook .kandyAddressContactList').append(div);
            } else {

                for (i = 0; i < results.length; i++) {

                    //if (results[i].friendStatus) {
                    contactListForPresence.push({full_user_id: results[i].contact_user_name});
                    //}

                    var id_attrib = results[i].contact_user_name.replace(/[.@]/g,'_');
                    $('.kandyAddressBook .kandyAddressContactList').append(
                        // HTML id can't contain @ and jquery doesn't like periods (in id)
                        "<div class='kandyContactItem' id='uid_" + results[i].contact_user_name.replace(/[.@]/g,'_') + "'>" +
                            "<span class='userid'>" + results[i].contact_user_name + "</span>" +
                            "<span id='presence_" + id_attrib + "' class='presence'></span>" +
                            "<input class='removeBtn' type='button' value='Remove' " +
                            " onclick='kandy_removeFromContacts(\"" + results[i].contact_id +"\")'>" +
                            "</div>"
                    );
                }
                KandyAPI.Phone.watchPresence(contactListForPresence);
            }
        },
        function() {
            alert("Error");
        }
    );

}
kandy_myStatusChanged = function(status) {
    KandyAPI.Phone.updatePresence(status);
}

var userIdToAddToContacts = null;  // need access to this in anonymous function below
kandy_addToContacts = function(userId) {
    userIdToAddToContacts = userId;

    // HTML id can't contain @ and jquery doesn't like periods (in id)
    if ($('#uid_' + userId.replace(/[.@]/g,'_')).length > 0) {
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
                        if (results[i].firstName) { contact['contact_first_name'] = results[i].firstName; }
                        if (results[i].lastName) { contact['contact_last_name'] = results[i].lastName; }
                        if (results[i].homePhone) { contact['contact_home_phone'] = results[i].homePhone; }
                        if (results[i].mobilePhone) { contact['contact_mobile_number'] = results[i].mobilePhone; }
                        if (results[i].workPhone) { contact['contact_business_number'] = results[i].workPhone; }
                        if (results[i].fax) { contact['contact_fax'] = results[i].fax; }
                        if (results[i].email) { contact['contact_email'] = results[i].email; }

                        KandyAPI.Phone.addToPersonalAddressBook(
                            contact,
                            kandy_loadContacts, // function to call on success
                            function (message) { alert("Error: " + message); }
                        );
                        break;
                    }
                }
            },
            function (statusCode) {
                alert("Error getting contact details: " + statusCode )
            }
        );
    }
};

kandy_removeFromContacts = function(nickname) {
    KandyAPI.Phone.removeFromPersonalAddressBook(nickname,
        kandy_loadContacts,  // function to call on success
        function(){alert("Error");}
    );
};

kandy_searchDirectoryByUserName = function () {
    var userName =$('.kandyAddressBook .kandyDirectorySearch #kandySearchUserName').val();
    KandyAPI.Phone.searchDirectoryByUserName(
        userName,
        function(results) {
            // clear out the results, but not the first line (results title)
            $(".kandyAddressBook .kandyDirSearchResults div:not(:first)").remove();
            var div = null;
            if (results.length == 0) {
                div = "<div class='kandyAddressBookNoResult'>-- No Matches Found --</div>";
                $('.kandyAddressBook .kandyDirSearchResults').append(div);
            } else {
                for (var i = 0; i < results.length; i++) {
                    $('.kandyDirSearchResults').append(
                        "<div>" +
                            "<span class='userId'>" + results[i].primaryContact + "</span>" +
                            "<input type='button' value='Add Contact' onclick='kandy_addToContacts(\"" +
                            results[i].primaryContact + "\")' />" +
                            "</div>"
                    );
                }
            }
        },
        function(val) {alert('Error');}
    );
};
/*
 Jquery Ready
 */
$(document).ready(function () {
    setup();
    login();
});