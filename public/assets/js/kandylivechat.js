/**
 * Created by Khanh on 28/5/2015.
 */

var LiveChatUI = {};
var checkAvailable;
var agent;

LiveChatUI.changeState = function(state){
    switch (state){
        case 'WAITING':
            $('#liveChat #waiting').show();
            $("#liveChat #registerForm").hide();
            $("#liveChat .customerService ,#liveChat #messageBox, #liveChat .formChat").hide();
            $("div.send-file span.icon-file").css("display", "none");
            break;
        case 'READY':
            $("#liveChat #registerForm").hide();
            $('#liveChat #waiting').hide();
            $("#liveChat .customerService, #liveChat #messageBox, #liveChat .formChat").show();
            $('#liveChat .agentName').html(agent.username);
            $('#liveChat .fullUserId').val(agent.full_user_id);
            $('#liveChat .currentStatus').val(1);
            if (!agent.full_user_id) {
                $(".liveChat .icon-file").hide();
            } else {
                $("div.send-file span.icon-file").css("display", "block");
            }
            $("#liveChat #messageBox li.their-message:first-child span.username").html(agent.username);
            $("#liveChat .handle.closeChat").show();
            checkAgentOnline();
            break;
        case "UNAVAILABLE":
            $("#liveChat #waiting p").html('There is something wrong, please try again later.');
            $("#liveChat #loading").hide();
            break;
        case "RECONNECTING":
            $('#liveChat .currentStatus').val(0);
            $("#liveChat #registerForm").hide();
            $("#liveChat .handle.closeChat").hide();
            $("#liveChat .customerService, #liveChat #messageBox, #liveChat .formChat").hide();
            $("#liveChat #waiting p").html('Chat agents not available, please wait...');
            $('#liveChat #waiting').show();
            $("#liveChat #loading").show();
            break;
        case "RATING":
            $("#liveChat #ratingForm").show();
            $("#liveChat .customerService, #liveChat #messageBox, #liveChat .formChat").hide();
            $("div.send-file span.icon-file").css("display", "none");
            break;
        case "ENDING_CHAT":
            $("#liveChat #ratingForm form").hide();
            $("#liveChat #ratingForm .formTitle").hide();
            $("#liveChat #ratingForm .message").show();
            break;
        default :
            $('#liveChat #registerForm').show();
            $("#liveChat .customerService, #liveChat #messageBox, #liveChat .formChat").hide();
            $("div.send-file span.icon-file").css("display", "none");
            checkAgentOnline();
            break;
    }
};

var loginLiveChat = function(domainApiKey, userName, password, success_callback, fail_callback) {
    if (typeof userName != 'undefined') {
        kandy.login(domainApiKey, userName, password, success_callback, fail_callback);
    }
};

var loginSSOLiveChat = function(userAccessToken, success_callback, failure, password) {
    kandy.loginSSO(userAccessToken, success_callback, failure, password);
};

var kandy_onMessageUser = function(msg){
    if(msg) {
        if (msg.messageType == 'chat') {
            var sender = agent.username;
            var message = msg.message.text;
            var messageBox = $("#messageBox");
            var newMessage = "<li class='their-message'><span class='username'>" + sender + ": </span>";

            if (msg.contentType === 'text' && msg.message.mimeType == 'text/plain') {
                newMessage += '<span class="imMessage">' + message + '</span>';
            } else {
                var fileUrl = kandy.messaging.buildFileUrl(msg.message.content_uuid);
                var html = '';
                if (msg.contentType == 'image') {
                    html = '<img src="' + fileUrl + '">';
                }
                html += '<a class="icon-download" href="' + fileUrl + '" target="_blank">' + msg.message.content_name + '</a>';
                newMessage += '<span class="imMessage">' + html + '</span>';
            }

            newMessage += '</li>';

            messageBox.find("ul").append(newMessage);
            messageBox.scrollTop(messageBox[0].scrollHeight);
        }
    }
};

// Gather the user input then send the image.
send_file_live_chat = function () {
    // Gather user input.
    var recipient = jQuery('#liveChat .fullUserId').val();
    var file = jQuery("#send-file")[0].files[0];

    if (file.type.indexOf('image') >=0) {
        kandy.messaging.sendImWithImage(recipient, file, onLiveChatFileSendSuccess, onLiveChatFileSendFailure);
    } else if (file.type.indexOf('audio') >=0) {
        kandy.messaging.sendImWithAudio(recipient, file, onLiveChatFileSendSuccess, onLiveChatFileSendFailure);
    } else if (file.type.indexOf('video') >=0) {
        kandy.messaging.sendImWithVideo(recipient, file, onLiveChatFileSendSuccess, onLiveChatFileSendFailure);
    } else if (file.type.indexOf('vcard') >=0) {
        kandy.messaging.sendImWithContact(recipient, file, onLiveChatFileSendSuccess, onLiveChatFileSendFailure);
    } else {
        kandy.messaging.sendImWithFile(recipient, file, onLiveChatFileSendSuccess, onLiveChatFileSendFailure);
    }
};

// What to do on a file send success.
function onLiveChatFileSendSuccess(message) {
    console.log(message.message.content_name + " sent successfully.");
    var messageBox = jQuery("#messageBox");
    var newMessage = "<li class='my-message'><span class='username'>Me: </span>";
    var fileUrl = kandy.messaging.buildFileUrl(message.message.content_uuid);
    var html = '';
    if (message.contentType == 'image') {
        html = '<img src="' + fileUrl + '">';
    }
    html += '<a class="icon-download" href="' + fileUrl + '" target="_blank">' + message.message.content_name + '</a>';
    newMessage += '<span class="imMessage">' + html + '</span>';
    newMessage += '</li>';

    messageBox.find("ul").append(newMessage);
    messageBox.scrollTop(messageBox[0].scrollHeight);
}

// What to do on a file send failure.
function onLiveChatFileSendFailure() {
    console.log("File send failure.");
}

var setupUser = function() {
    kandy.setup({
        listeners: {
            message: kandy_onMessageUser
        }
    });
};


var logout = function(){
    kandy.logout();
};
var login_chat_success_callback = function (){
    console.log('login successful');
    LiveChatUI.changeState("READY");
};
var login_chat_fail_callback = function (){
    console.log('login failed');
    LiveChatUI.changeState("UNAVAILABLE");
};

var getKandyUsers = function(){
    $.ajax({
        url: baseUrl + '/kandy/getFreeUser',
        type: 'GET',
        dataType: 'json',
        success: function(res){
            if(!checkAvailable){
                LiveChatUI.changeState('WAITING');
            }
            if(res.status == 'success'){
                if(checkAvailable){
                    clearInterval(checkAvailable);
                }
                var username = res.user.full_user_id.split('@')[0];
                if(username.indexOf("anonymous") >= 0) {
                    var user_access_token = res.user.user_access_token;
                    console.log('loginSSO ...');
                    loginSSOLiveChat(user_access_token, login_chat_success_callback, login_chat_fail_callback, res.user.password);
                } else {
                    console.log('login ...');
                    loginLiveChat(res.apiKey, username, res.user.password, login_chat_success_callback, login_chat_fail_callback);
                }
                setupUser();
                agent = res.agent;
                heartBeat(60000);
            }else{
                if (res.message) {
                    console.log('Error! ' + res.message);
                }
                if(!checkAvailable){
                    checkAvailable = setInterval(getKandyUsers, 5000);
                } else {
                    LiveChatUI.changeState('RECONNECTING');
                }
            }
        },
        error: function(){
            LiveChatUI.changeState("UNAVAILABLE");
        }
    })
};

var checkAgentOnline = function() {
    var current_full_user_id = $('#liveChat .fullUserId').val();
    var current_status = $('#liveChat .currentStatus').val();

    $.ajax({
        url: baseUrl + '/kandy/checkAgentOnline',
        type: 'GET',
        data: {full_user_id : current_full_user_id},
        dataType: 'json',
        success: function(res){
            if(res.isOnline == true && ((current_status == 1 && current_full_user_id != res.full_user_id)
                || current_status == 0)){
                agent = res.agent;
                if (current_full_user_id != '') {
                    LiveChatUI.changeState('READY');
                }
            } else if(current_status == 1 && res.isOnline == false) {
                if (current_full_user_id != '') {
                    LiveChatUI.changeState('RECONNECTING');
                }
            }

            setTimeout(checkAgentOnline, 10000);
        },
        error: function(){
            if (current_full_user_id != '') {
                LiveChatUI.changeState("UNAVAILABLE");
            }
        }
    });
};

var endChatSession = function(){
    LiveChatUI.changeState('ENDING_CHAT');
    logout();
    $.ajax({
        url: baseUrl + '/kandy/endChatSession',
        type: 'GET',
        success: function(data){
            console.log(data);
            window.location.reload();
        }
    });
};

var sendIM = function(username, message){
    var contentChat = $("#messageToSend").val();
    if(contentChat.trim().length > 0) {
        kandy.messaging.sendIm(username, message, function () {
                var messageBox = $("#messageBox");
                messageBox.find("ul").append("<li class='my-message'><span class='username'>Me: </span>"+$("#messageToSend").val()+"</li>");
                $("#formChat")[0].reset();
                messageBox.scrollTop(messageBox[0].scrollHeight);
            },
            function () {
                alert("IM send failed");
            }
        );
    }
};

var heartBeat = function(interval){
    return setInterval(function(){
        $.get('/kandy/stillAlive');
    },parseInt(interval));
};

$(function(){

    if ($("#liveChat").length) {
        $(document).on('change', "#liveChat input[type=file]", function (e){
            var fileName = $(this).val();
            if (fileName != '') {
                send_file_live_chat();
            }
        });
    }

    var elementsBlock = [];
    //$(window).bind('beforeunload', endChatSession);
    toggleLiveChat();
    //hide vs restore box chat
    $(".minimize.handle").click(function(){
        elementsBlock = [];
        $('.liveChatBody > div').each(function(index, value) {
            if($(this).is(":visible")) {
                elementsBlock.push($(this));
                $(this).hide();
            }
        });
        $('#liveChat #restoreBtn').css('display', 'block');
        $('#liveChat .minimize').css('display', 'none');
    });

    $("#restoreBtn").click(function(){
        if(elementsBlock.length > 0) {
            for(var i = 0; i < elementsBlock.length; i++) {
                elementsBlock[i].show();
            }
        } else {
            $("#registerForm").toggleClass('hidden');
        }
        $('#liveChat #restoreBtn').css('display', 'none');
        $('#liveChat .minimize').css('display', 'block');
    });

    function toggleLiveChat() {
        if($("#registerForm").hasClass('hidden')) {
            $('#liveChat #restoreBtn').css('display', 'block');
            $('#liveChat .minimize').css('display', 'none');
        } else {
            $('#liveChat #restoreBtn').css('display', 'none');
            $('#liveChat .minimize').css('display', 'block');
        }
    };

    $(".handle.closeChat").click(function(){
        if(!$('#ratingForm').is(":visible")) {
            LiveChatUI.changeState('RATING');
        } else {
            $('#liveChat').hide();
        }
    });

    $("#customerInfo").on('submit', function(e){
        var form = $(this);
        e.preventDefault();
        $.ajax({
            url: baseUrl + form.attr('action'),
            data: form.serialize(),
            type: 'POST',
            beforeSend: function(xhr) {
                LiveChatUI.changeState('WAITING');
            },
            success: function(res){
                if(res.hasOwnProperty('errors')){
                    form.find("span.error").empty().hide();
                    for(var e in res.errors){
                        form.find('span[data-input="'+e+'"]').html(res.errors[e]).show();
                    }
                }else{
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
    });
    $('#liveChat #ratingForm #btnSendRate').click(function(e){
        e.preventDefault();
        if(rateData.rate) {
            rateData.rate.id = agent.main_user_id;
        } else {
            rateData.rate = {id : agent.main_user_id};
        }
        rateData['_token'] = _token;
        var rateComment = $("#liveChat #rateComment").val();
        if(rateComment){
            rateData.comment = rateComment
        }
        $.ajax({
            url: baseUrl + '/kandy/rateagent',
            data: rateData,
            type: 'POST',
            success: function (res){
                if(res.success){
                    endChatSession();
                }
            }
        })
    })
});
