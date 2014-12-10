<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script src="https://kandy-portal.s3.amazonaws.com/public/javascript/fcs/1.0.0/fcs.js"></script>
<script src="https://kandy-portal.s3.amazonaws.com/public/javascript/kandy/1.1.2/kandy.js"></script>

<script language="JavaScript">
    // this is called when page is done loading to set up (initialize) the KandyAPI.Phone
    setup = function () {
        // initialize KandyAPI.Phone, passing a config JSON object that contains listeners (event callbacks)
        KandyAPI.Phone.setup({
            // respond to Kandy events...
            listeners: {
                loginsuccess: function () {
                    changeUIState('READY_FOR_CALLING');
                },
                loginfailed: function () {
                    alert("Login failed");
                },
                callincoming: function (call, isAnonymous) {
                    if (!isAnonymous) {
                        $('#otherPartyName').val(call.callerName);
                    } else {
                        $('#otherPartyName').val('anonymous');
                    }
                    changeUIState('BEING_CALLED');
                },
                // when an outgoing call is connected
                oncall: function (call) {
                    changeUIState("ON_CALL");
                },
                // when an incoming call is connected
                // you indicated that you are answering the call
                callanswered: function (call, isAnonymous) {
                    changeUIState("ON_CALL");
                },
                callended: function () {
                    $('#theirVideo').empty();
                    changeUIState('READY_FOR_CALLING');
                },
                localvideoinitialized: function (videoTag) {
                    $('#myVideo').append(videoTag);
                },
                // a video tag is being provided (required for both audio and video calls)
                // you must insert it into the DOM for communication to happen (although for audio calls, it can remain hidden)
                remotevideoinitialized: function (videoTag) {
                    $('#theirVideo').append(videoTag);
                }

            }
        });
    }

    login = function () {
        KandyAPI.Phone.login($("#domainApiId").val(), $("#logInId").val(), $('#passwd').val());
    }

    logout = function () {
        KandyAPI.Phone.logout(function () {
            changeUIState('LOGGED_OUT');
        });
    }

    answerVideoCall = function () {
        changeUIState("ANSWERING_CALL");
        KandyAPI.Phone.answerVideoCall();
    }

    makeCall = function () {
        KandyAPI.Phone.makeVideoCall($('#callOutUserId').val());
        changeUIState('CALLING');
    }

    endCall = function () {
        KandyAPI.Phone.endCall();
        changeUIState('READY_FOR_CALLING');
    }

    changeUIState = function (state) {
        switch (state) {
            case 'LOGGED_OUT':
                $('#logInForm').show();
                $('#loggedIn').hide();
                $('#someonesCalling').hide();
                $("#callOut").hide();
                $("#calling").hide();
                $('#onCall').hide();
                break;
            case 'READY_FOR_CALLING':
                $('#logInForm').hide();
                $('#loggedIn').show();
                $('#someonesCalling').hide();
                $('#callOut').show();
                $('#calling').hide();
                $('#onCall').hide();
                $('#loggedInAs').text($('#logInId').val());
                break;
            case 'BEING_CALLED':
                $('#logInForm').hide();
                $('#loggedIn').hide();
                $('#someonesCalling').show();
                $('#callOut').hide();
                $('#calling').hide();
                $('#onCall').hide();
                break;
            case 'CALLING':
                $('#logInForm').hide();
                $('#loggedIn').hide();
                $('#someonesCalling').hide();
                $('#callOut').hide();
                $('#calling').show();
                $('#onCall').hide();
                break;
            case 'ON_CALL':
                $('#logInForm').hide();
                $('#loggedIn').hide();
                $('#someonesCalling').hide();
                $('#callOut').hide();
                $('#calling').hide();
                $('#onCall').show();
                break;
        }
    }

</script>


<div onload="setup();">
    <style>
        #videos {
            width: 675px
        }

        #theirVideo {
            background-color: darkslategray;
            width: 334px;
            height: 250px;
            display: inline-block;
        }

        #myVideo {
            background-color: darkslategray;
            width: 334px;
            height: 250px;
            display: inline-block;
            float: right
        }

        #meLabel {
            width: 340px;
            text-align: right;
            display: inline-block
        }
    </style>


    <h2>Quick Start Sample App: Answer a Video Call</h2>
    This sample application demonstrates the code for answering a video call with Kandy.
    <br/>
    <hr>
    <br/>

    <div id="loggedIn" style="display:none">
        Hello <span id="loggedInAs"></span>.
        <input id="logoutBtn" type="button" value="Log Out" onclick="logout();return false;"
               style="width:90px;height:23px;"/>
    </div>

    <form id="logInForm">
        Project API Key: <input id="domainApiId" type="text" style="width:200px;margin-bottom:1px;" value=""/><br/>
        Username: <input id="logInId" type="text" style="width:200px;" value=""/><br/>
        Password: <input id="passwd" type="password" style="width:200px;" value=""/><br/>
        <input id="loginBtn" type="button" value="Log in" onclick="login();return false;"
               style="width:90px;height:23px;/>
    </form>

    <div id="someonesCalling" style="display:none">
        <br/><br/>
        Incoming Call
        <input id="answerVideoCallBtn" type="button" value="Answer" onclick="answerVideoCall()"
               style="width:90px;height:23px;"/>
        <input id="otherPartyName" type="hidden"/><br/><br/>
</div>

<div id="callOut" style="display:none">
    <br/>
    User to call: <input id="callOutUserId" type="text" value=""/>
    <input id="callBtn" type="button" value="Call" onclick="makeCall()" style="width:90px;height:23px;"/><br/><br/>
</div>

<span id="calling" style="display:none">
        <br/><br/>
        Calling...<input type="button" value="End Call" onclick="endCall()" style="width:90px;height:23px;"/><br/><br/>
    </span>

<span id="onCall" style="display:none">
        <br/><br/>
        You're connected! <input type="button" value="End Call" onclick="endCall()"
                                 style="width:90px;height:23px;"/><br/><br/>
    </span>

<div id="videos">
    Them:<span id="meLabel">Me:</span>
    <span id="theirVideo" style="display:inline-block"></span>
    <span id="myVideo"></span>
</div>
</div>
