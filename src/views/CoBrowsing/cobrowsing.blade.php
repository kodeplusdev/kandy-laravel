<div id="coBrowsing">
    <button class="small tiny modalToggle" data-reveal-id="sessionModal">Create Session</button>
    <div>
        <div class="openSessionWrap">
            <label>Available sessions</label>
            <select id="{{$sessionListId}}"></select>
        </div>
        <div class="buttons">
            <button class="small" id="{{$btnConnectSessionId}}">Connect</button>
            <button class="small" id="{{$btnTerminateId}}">Terminate</button>
            <button class="small" id="{{$btnStartCoBrowsingId}}">Start co-browsing</button>
            <button class="small" id="{{$btnStartBrowsingViewerId}}">Start co-browsing viewer</button>
            <button class="small" id="{{$btnStopId}}">Stop co-browsing</button>
            <button class="small" id="{{$btnLeaveId}}">Leave</button>
        </div>
    </div>
    <div id="{{$holderId}}"></div>
</div>
{!! HTML::script(\Kodeplus\Kandylaravel\Kandylaravel::KANDY_CO_BROWSE) !!}
<script>
    var openSessions = [];
    var currentSession;
    var myOwnSessions = [];// sessions that current user created
    var mySessions    = [];// sessions that current user is a participant
    var browsingType;
    var currentKandyUser = '{{$currentUser->user_id . '@' . $currentUser->domain_name}}';
    var sessionListeners = {
        'onUserJoinRequest': kandy_onSessionJoinRequest,
        'onJoinApprove': kandy_onSessionJoinApprove
    };
    function displayButtons(){
        var isAdmin = false, isMember = false;
        currentSession = openSessions[parseInt($("#openSessions").val())];
        if(typeof  currentSession != 'undefined'){
            isAdmin  = myOwnSessions.indexOf(currentSession.session_id) > -1;
            isMember = (mySessions.indexOf(currentSession.session_id) > -1 && !isAdmin);
        }

        //if current user is owner of this session
        if(isAdmin){
            $("#coBrowsing .buttons #<?php echo $btnTerminateId;?>").show();
            $("#coBrowsing .buttons #<?php echo $btnStartCoBrowsingId?>").show();
            $("#coBrowsing .buttons #<?php echo $btnConnectSessionId?>").hide();
            $("#coBrowsing .buttons #<?php echo $btnStartBrowsingViewerId?>").hide();
            $("#coBrowsing .buttons #<?php echo $btnLeaveId?>").hide();
        }else{
            if(isMember){
                $("#coBrowsing .buttons #<?php echo $btnStartBrowsingViewerId?>").show();
                $("#coBrowsing .buttons #<?php echo $btnConnectSessionId?>").hide();
                $("#coBrowsing .buttons #<?php echo $btnStartCoBrowsingId?>").hide();
                $("#coBrowsing .buttons #<?php echo $btnTerminateId;?>").hide();
                $("#coBrowsing .buttons #<?php echo $btnLeaveId?>").show();
            }else {
                $("#coBrowsing .buttons #<?php echo $btnConnectSessionId?>").show();
                $("#coBrowsing .buttons #<?php echo $btnStartCoBrowsingId?>").hide();
                $("#coBrowsing .buttons #<?php echo $btnStartBrowsingViewerId?>").hide();
                $("#coBrowsing .buttons #<?php echo $btnTerminateId;?>").hide();
                $("#coBrowsing .buttons #<?php echo $btnLeaveId?>").hide();
            }
        }
    }

    var loadSessionList = function(sessions) {
        var i = 0;
        var sessionList = $("#<?php echo $sessionListId ?>");
        sessionList.empty();
        openSessions = [];
        if(sessions.length){
            sessions.forEach(function(session){
                //only use session with type = cobrowsing
                if(session.session_type == 'cobrowsing'){
                    openSessions.push(session);
                    if((session.admin_full_user_id == currentKandyUser) && (myOwnSessions.indexOf(session.session_id) == -1)){
                        myOwnSessions.push(session.session_id);
                    }
                    kandy_getSessionInfo(session.session_id,function(result){
                        result.session.participants.forEach(function(p){
                            if((p.full_user_id == currentKandyUser) && (mySessions.indexOf(session.session_id) == -1)){
                                mySessions.push(session.session_id);
                            }
                        })
                    });
                    KandyAPI.Session.setListeners(session.session_id,sessionListeners);
                    var option = $("<option>").val(i).text(session.session_name || session.session_id);
                    sessionList.append(option);
                    i++;
                }
            });
            setTimeout(displayButtons,3000);
        }

    };
    var sessionJoinApprovedCallback = function(sessionId) {
        mySessions.push(sessionId);
        displayButtons();
    };
        /* Document ready */
        $(function(){

            $(document).on("click", "#<?php echo $btnConnectSessionId?>", function(e){
                currentSession = openSessions[parseInt($("#openSessions").val())];
                kandy_joinSession(currentSession.session_id);
            });
            $(document).on('change', "#<?php echo $sessionListId ?>",displayButtons);

            $(document).on('click', "#coBrowsing #<?php echo $btnTerminateId;?>", function(e){
                var confirm = window.confirm("are you sure to terminate this session?")
                if(confirm){
                    var session = openSessions[parseInt($("#openSessions").val())];
                    myOwnSessions.splice(myOwnSessions.indexOf(session.session_id,1));
                    mySessions.splice(mySessions.indexOf(session.session_id),1);
                    kandy_terminateSession(session.session_id, getCoBrowsingSessions);
                }
            });
            $(document).on('click', "#coBrowsing #<?php echo $btnStartCoBrowsingId?>", function(e){
                if(currentSession){
                    $("#coBrowsing").addClass("browsing");
                    $("#<?php echo $sessionListId ?>").attr("disabled", true);
                    browsingType = 'user';
                    kandy_startCoBrowsing(currentSession.session_id);
                }
            });
            $(document).on('click', "#coBrowsing #<?php echo $btnStartBrowsingViewerId?>", function(e){
                if(currentSession){
                    browsingType = 'agent';
                    $("#<?php echo $sessionListId ?>").attr("disabled", true);
                    $("#coBrowsing").addClass("browsing");
                    kandy_startCoBrowsingAgent(currentSession.session_id, document.getElementById("<?php echo $holderId ?>"));
                }
            });

            $(document).on('click', "#coBrowsing #<?php echo $btnStopId ?>", function(e){
                $("#coBrowsing").removeClass("browsing");
                try{
                    if(browsingType == 'user'){
                        kandy_stopCoBrowsing();
                    }else if(browsingType == 'agent'){
                        kandy_stopCoBrowsingAgent();
                    }
                }catch(e){
                    console.log("Error:");
                    console.log(e);
                }finally {
                    $("#<?php echo $sessionListId ?>").attr("disabled", false);
                }
            });
            $(document).on('click', "#coBrowsing #<?php echo $btnLeaveId?>", function(e){
                var confirm = window.confirm("Are you sure to leave this session?");
                if(confirm){
                    if(currentSession){
                        //delete from my session array
                        mySessions.splice(mySessions.indexOf(currentSession),1);
                        kandy_LeaveSession(currentSession.session_id);
                        displayButtons();
                    }
                }
            })
        });
</script>