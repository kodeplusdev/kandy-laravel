setup = function() {
    // initialize KandyAPI.Phone, passing a config JSON object that contains listeners (event callbacks)
    KandyAPI.Phone.setup({
        // respond to Kandy events...
        listeners: {
            loginsuccess: loginsuccess_callback,
            loginfailed: loginfailed_callback,
            callincoming: callincoming_callback,
            // when an outgoing call is connected
            oncall: oncall_callback,
            // when an incoming call is connected
            // you indicated that you are answering the call
            callanswered: callanswered_callback,
            callended: callended_callback ,
            localvideoinitialized: localvideoinitialized_callback,
            // a video tag is being provided (required for both audio and video calls)
            // you must insert it into the DOM for communication to happen (although for audio calls, it can remain hidden)
            remotevideoinitialized: remotevideoinitialized_callack
        }
    });
}

answerVideoCall = function() {
    changeUIState("ANSWERING_CALL");
    KandyAPI.Phone.answerVideoCall();
}

makeCall = function() {
    KandyAPI.Phone.makeVideoCall($('#callOutUserId').val());
    changeUIState('CALLING');
}

endCall = function() {
    KandyAPI.Phone.endCall();
    changeUIState('READY_FOR_CALLING');
}
$(document).ready(function(){
    setup();
    login();
});