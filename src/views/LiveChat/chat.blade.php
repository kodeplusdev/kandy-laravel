{!! HTML::style(asset(\Kodeplus\Kandylaravel\Kandylaravel::KANDY_CSS_LIVE_CHAT)) !!}
{!! HTML::style(asset(\Kodeplus\Kandylaravel\Kandylaravel::RATE_CSS)) !!}
<div id="liveChat">
    <div class="header">
        Kandy live chat
        <span class="closeChat handle" title="end chat" style="display: none">x</span>
        <span class="minimize handle" title="minimize">_</span>
        <span id="restoreBtn"></span>
    </div>
    <input type="hidden" class="currentStatus" value="0">
    <div class="liveChatBody">
        <div id="waiting">
            <img id="loading" width="30px" height="30px" src="{{asset('kandy-io/kandy-laravel/assets/img/loading.gif')}}" title="loading">
            <p>Please wait a moment...</p>
        </div>
        <div id="registerForm" class="@if(!\Session::has('kandyLiveChatUserInfo')) hidden @endif">
            <form id="customerInfo" method="POST" action="/kandy/registerGuest" >
                <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                <label for="customerName">{{$registerForm['name']['label']}}</label>
                <input type="text" name="customerName" id="customerName" class="{{$registerForm['name']['class']}}" />
                <span data-input="customerName" style="display: none" class="error"></span>
                <label for="customerEmail">{{$registerForm['email']['label']}}</label>
                <input type="text" name="customerEmail" id="customerEmail" class="{{$registerForm['email']['class']}}" />
                <span data-input="customerEmail" style="display: none" class="error"></span>
                <button type="submit">Start chat</button>
            </form>
        </div>
        <div id="ratingForm">
            <h3 class="formTitle">Rate for <span class="agentName"></span> </h3>
            <form>
                <select id="backing2b">
                    <option title="" value="1">1</option>
                    <option title="" value="2">2</option>
                    <option title="" value="3">3</option>
                    <option title="" value="4">4</option>
                    <option title="" value="5" selected="selected">5</option>
                </select>
                <div class="rateit" id="rateitComment" data-rateit-backingfld="#backing2b"></div>
                <textarea id="rateComment" rows="3" placeholder="Say something about your supporter"></textarea>
                <a id="btnEndSession" class="button" href="{{route('kandy.endChatSession')}}">No, thanks</a>
                <button id="btnSendRate" type="submit">Send</button>
            </form>
            <div class="message">
                <h3>Thanks you! Good bye!</h3>
            </div>
        </div>

        <div class="customerService">
            <div class="avatar">
                <img src="{{$agentInfo['avatar']}}">
            </div>
            <div class="helpdeskInfo">
                <span class="agentName"></span>
                <input type="hidden" class="fullUserId">
                <p class="title">{{$agentInfo['title']}}</p>
            </div>
        </div>
        <div id="messageBox" class="" style="">
            <ul>
                <li class="their-message"><span class="username"></span>: Hi {{\Session::get('userInfo.username')}}, what brings you here?</li>
            </ul>
        </div>
        <div class="formChat" style="">
            <form id="formChat">
                <input type="text" value="" name="message" id="messageToSend" placeholder="Type here and press Enter to send">
            </form>
        </div>
    </div>
</div>
{!! HTML::script(\Kodeplus\Kandylaravel\Kandylaravel::KANDY_JS) !!}
{!! HTML::script(\Kodeplus\Kandylaravel\Kandylaravel::KANDY_JS_LIVE_CHAT) !!}
{!! HTML::script(\Kodeplus\Kandylaravel\Kandylaravel::RATE_JS) !!}

<script type="text/javascript">
    //agent user id
    var rateData = {};
    $("#rateitComment").bind('rated', function(event, value){
        var ri = $(this);
        rateData = rateData || {};
        rateData.rate = {point: value}
    });

    $(".rateit").bind('reset', function(){
        rateData = rateData || {};
        if(rateData.hasOwnProperty('rate')){
            delete rateData.rate;
        }
    });


    $(function(){
        @if(\Session::has('kandyLiveChatUserInfo'))
            getKandyUsers();
        @else
            //default ui state
            LiveChatUI.changeState();
        @endif

        @if(\Session::has('kandyLiveChatUserInfo.user'))
            var stillAlive = heartBeat(60000);
        @endif

    });
</script>