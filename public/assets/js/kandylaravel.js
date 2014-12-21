/*
KANDY SETUP AND LISTENER CALLBACK
 */
setup = function () {
    // initialize KandyAPI.Phone, passing a config JSON object that contains listeners (event callbacks)
    KandyAPI.Phone.setup({
        // respond to Kandy events...
        listeners: {
            loginsuccess: loginsuccess_callback,
            loginfailed: loginfailed_callback,
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
            remotevideoinitialized: remotevideoinitialized_callack
        }
    });
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

/*
 Jquery Ready
 */
$(document).ready(function () {
    setup();
    login();
});