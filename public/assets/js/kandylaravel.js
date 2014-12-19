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
            callincoming: callincoming_callback,
            // when an outgoing call is connected
            oncall: oncall_callback,
            // when an incoming call is connected
            // you indicated that you are answering the call
            callanswered: callanswered_callback,
            callended: callended_callback,
            localvideoinitialized: localvideoinitialized_callback,
            // a video tag is being provided (required for both audio and video calls)
            // you must insert it into the DOM for communication to happen (although for audio calls, it can remain hidden)
            remotevideoinitialized: remotevideoinitialized_callack
        }
    });
}

changeAnswerButtonState = function (state) {
    switch (state) {
        case 'READY_FOR_CALLING':

            $('.kandyVideoAnswerButton .kandyVideoButtonSomeonesCalling').hide();
            $('.kandyVideoAnswerButton .kandyVideoButtonCallOut').show();
            $('.kandyVideoAnswerButton .kandyVideoButtonCalling').hide();
            $('.kandyVideoAnswerButton .kandyVideoButtonOnCall').hide();
            break;
        case 'BEING_CALLED':
            $('.kandyVideoAnswerButton .kandyVideoButtonSomeonesCalling').show();
            $('.kandyVideoAnswerButton .kandyVideoButtonCallOut').hide();
            $('.kandyVideoAnswerButton .kandyVideoButtonCalling').hide();
            $('.kandyVideoAnswerButton .kandyVideoButtonOnCall').hide();
            break;
        case 'CALLING':
            $('.kandyVideoAnswerButton .kandyVideoButtonSomeonesCalling').hide();
            $('.kandyVideoAnswerButton .kandyVideoButtonCallOut').hide();
            $('.kandyVideoAnswerButton .kandyVideoButtonCalling').show();
            $('.kandyVideoAnswerButton .kandyVideoButtonOnCall').hide();
            break;
        case 'ON_CALL':
            $('.kandyVideoAnswerButton .kandyVideoButtonSomeonesCalling').hide();
            $('.kandyVideoAnswerButton .kandyVideoButtonCallOut').hide();
            $('.kandyVideoAnswerButton .kandyVideoButtonCalling').hide();
            $('.kandyVideoAnswerButton .kandyVideoButtonOnCall').show();
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

    KandyAPI.Phone.makeVideoCall($('.kandyVideoAnswerButton .kandyVideoButtonCallOut #callOutUserId').val());
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
/*
 Jquery Ready
 */
$(document).ready(function () {
    setup();
    login();
});