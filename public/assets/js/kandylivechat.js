/**
 * Created by Khanh on 28/5/2015.
 */

var LiveChatUI = {};
var checkAvailable;
LiveChatUI.changeState = function(state){
    switch (state){
        case 'WAITING':
            $('#liveChat #waiting').show();
            $("#liveChat #registerForm").hide();
            $("#liveChat .customerService ,#liveChat #messageBox, #liveChat .formChat").hide();
            break;
        case 'READY':
            $("#liveChat #registerForm").hide();
            $('#liveChat #waiting').hide();
            $("#liveChat .customerService, #liveChat #messageBox, #liveChat .formChat").show();
            $('#liveChat .agentName').html(agent.username);
            $("#liveChat #messageBox li.their-message span.username").html(agent.username);
            $("#liveChat .handle.closeChat").show();
            break;
        case "UNAVAILABLE":
            $("#liveChat #waiting p").html('There is something wrong, please try again later.');
            $("#liveChat #loading").hide();
            break;
        case "RECONNECTING":
            $("#liveChat #waiting p").html('Chat agents not available, please wait...');
            $("#liveChat #loading").show();
            break;
        case "RATING":
            $("#liveChat #ratingForm").show();
            $("#liveChat .customerService, #liveChat #messageBox, #liveChat .formChat").hide();
            break;
        case "ENDING_CHAT":
            $("#liveChat #ratingForm form").hide();
            $("#liveChat #ratingForm .formTitle").hide();
            $("#liveChat #ratingForm .message").show();
            break;
        default :
            $('#liveChat #registerForm').show();
            $("#liveChat .customerService, #liveChat #messageBox, #liveChat .formChat").hide();
            break;
    }
};

var login = function(domainApiKey, userName, password, success_callback, fail_callback) {
    kandy.login(domainApiKey, userName, password, success_callback, fail_callback);
};

var kandy_onMessage = function(msg){
    if(msg) {
        if (msg.messageType == 'chat') {
            var sender = agent.username;
            var message = msg.message.text;
            var messageBox = $("#messageBox");
            messageBox.find("ul").append("<li class='their-message'><span class='username'>" + sender + ": </span>" + message + "</li>");
            messageBox.scrollTop(messageBox[0].scrollHeight);
        }
    }
};

var setup = function() {
    kandy.setup({
        listeners: {
            message: kandy_onMessage
        }
    });
};


var logout = function(){
    kandy.logout();
};
var login_success_callback = function (){
    console.log('login successful')
    LiveChatUI.changeState("READY");
};
var login_fail_callback = function (){
    console.log('login failed')
    LiveChatUI.changeState("UNAVAILABLE");
};

var getKandyUsers = function(){
    $.ajax({
        url:'/kandy/getFreeUser',
        type: 'GET',
        dataType: 'json',
        async: false,
        success: function(res){
            if(checkAvailable){
                LiveChatUI.changeState('RECONNECTING');
            }else{
                LiveChatUI.changeState('WAITING');
            }
            if(res.status == 'success'){
                if(checkAvailable){
                    clearInterval(checkAvailable);
                }
                var username = res.user.full_user_id.split('@')[0];
                login(res.apiKey, username, res.user.password, login_success_callback, login_fail_callback);
                setup();
                agent = res.agent;
                heartBeat(60000);
            }else{
                if(!checkAvailable){
                    checkAvailable = setInterval(getKandyUsers, 5000);
                }
            }
        },
        error: function(){
            LiveChatUI.changeState("UNAVAILABLE");
        }
    })
};

var endChatSession = function(){
    LiveChatUI.changeState('ENDING_CHAT');
    logout();
    $.ajax({
        url: '/kandy/endChatSession',
        type: 'GET',
        async: false,
        success: function(data){
            console.log(data);
        }
    });
};

var sendIM = function(username, message){
    KandyAPI.Phone.sendIm(username, message, function () {
            var messageBox = $("#messageBox");
            messageBox.find("ul").append("<li class='my-message'><span class='username'>Me: </span>"+$("#messageToSend").val()+"</li>");
            $("#formChat")[0].reset();
            messageBox.scrollTop(messageBox[0].scrollHeight);
        },
        function () {
            alert("IM send failed");
        }
    );
};

var heartBeat = function(interval){
    return setInterval(function(){
        $.get('/kandy/stillAlive');
    },parseInt(interval));
};

$(function(){
    //$(window).bind('beforeunload', endChatSession);

    //hide vs restore box chat
    $(".handle.minimize, #restoreBtn").click(function(){
        $("#liveChat").toggleClass('hidden');
    });

    $(".handle.closeChat").click(function(){
        LiveChatUI.changeState('RATING');
    });

    $("#customerInfo").on('submit', function(e){
        var form = $(this);
        e.preventDefault();
        $.ajax({
            url: form.attr('action'),
            data: form.serialize(),
            type: 'POST',
            success: function(res){
                if(res.hasOwnProperty('errors')){
                    form.find("span.error").empty().hide();
                    for(var e in res.errors){
                        form.find('span[data-input="'+e+'"]').html(res.errors[e]).show();
                    }
                }else{
                    LiveChatUI.changeState('WAITING');
                    getKandyUsers();
                }
            }
        })
    });

    //form chat submit handle
    $("#formChat").on('submit', function(e){
        e.preventDefault();
        sendIM(agent.full_user_id, $("#messageToSend").val());
    });

    /** Rating for agents JS code **/
    $("#liveChat #ratingForm #btnEndSession").click(function(e){
        e.preventDefault();

        endChatSession();
        window.location.reload();
    });
    $('#liveChat #ratingForm #btnSendRate').click(function(e){
        e.preventDefault();
        rateData = rateData || {};
        var rateComment = $("#liveChat #rateComment").val();
        if(rateComment){
            rateData.comment = rateComment
        }
        $.ajax({
            url: '/kandy/rateagent',
            data: rateData,
            type: 'POST',
            success: function (res){
                if(res.success){
                    endChatSession();
                    window.location.reload();
                }
            }
        })
    })
});
