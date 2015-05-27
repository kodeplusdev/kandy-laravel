/**
 * Created by Khanh on 28/5/2015.
 */

var LiveChatUI = {};
var checkAvailable;
LiveChatUI.changeState = function(state){
    switch (state){
        case 'WAITING':
            console.log('waiting');
            $('#liveChat #waiting').show();
            $("#liveChat #registerForm").hide();
            $("#liveChat .customerService ,#liveChat #messageBox, #liveChat .formChat").hide();
            break;
        case 'READY':
            console.log('ready');
            $("#liveChat #registerForm").hide();
            $('#liveChat #waiting').hide();
            $("#liveChat .customerService, #liveChat #messageBox, #liveChat .formChat").show();
            $('#liveChat #agentName').html(agent.split('@')[0]);
            $("#liveChat #messageBox li.their-message span.username").html(agent.split('@')[0]);
            $("#liveChat .handle.closeChat").show();
            break;
        case "UNAVAILABLE":
            console.log('unavailable');
            $("#liveChat #waiting p").html('There is something wrong, please try again later.');
            $("#liveChat #loading").hide();
            break;
        case "RECONNECTING":
            $("#liveChat #waiting p").html('Chat agents not available, please wait...');
            $("#liveChat #loading").show();
            break;

        default :
            $('#liveChat #registerForm').show();
            $("#liveChat .customerService, #liveChat #messageBox, #liveChat .formChat").hide();
            break;
    }
};

var login = function(domainApiKey, userName, password, success_callback) {
    kandy.login(domainApiKey, userName, password, success_callback);
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
                console.log('username:'+username);
                console.log('agent:'+res.agent.full_user_id);
                login(res.apiKey, username, res.user.password, login_success_callback, login_fail_callback);
                agent = res.agent.full_user_id;
                setInterval(getIm, 3000);
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
    logout();
    $.ajax({
        url: '/kandy/endChatSession',
        type: 'GET',
        success: function(){
            window.location.reload();
        }
    });

};

var sendIM = function(username, message){
    if(username == '') username = 'khanhhuynh@khanhht.gmail.com';
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

var chatting = function(){
    $.ajax({
        url: '/kandy/chatting',
        type: 'GET',
        success: function(data){
            console.log(data);
        }
    })
};

var getIm = function(){
    KandyAPI.Phone.getIm(
        //success callback
        function(data){
            if(data.messages.length){
                for(var i = 0; i< data.messages.length; i++){
                    var msg = data.messages[i];
                    if(msg.messageType == 'chat'){
                        var sender = msg.sender.user_id;
                        var message = msg.message.text;
                        var messageBox = $("#messageBox");
                        messageBox.find("ul").append("<li class='their-message'><span class='username'>"+sender+": </span>"+message+"</li>");
                        messageBox.scrollTop(messageBox[0].scrollHeight);
                        chatting();
                    }
                }
            }
        },
        //fail callback
        function(){

        }

    )
};

$(function(){
    //hide vs restore box chat
    $(".handle.minimize, #restoreBtn").click(function(){
        $("#liveChat").toggleClass('hidden');
    });

    $(".handle.closeChat").click(function(){
        endChatSession();
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
        sendIM(agent, $("#messageToSend").val());
    })
});
