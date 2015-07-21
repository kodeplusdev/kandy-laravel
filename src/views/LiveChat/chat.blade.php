{{HTML::style(asset(\Kodeplusdev\Kandylaravel\Kandylaravel::KANDY_CSS_LIVE_CHAT))}}
{{HTML::style(asset(\Kodeplusdev\Kandylaravel\Kandylaravel::RATE_CSS))}}
<div id="liveChat" class="@if(!\Session::has('kandyLiveChatUserInfo')) hidden @endif">
    <div class="header">
        Kandy live chat
        <span class="closeChat handle" title="end chat" style="display: none">x</span>
        <span class="minimize handle" title="minimize">_</span>
        <span id="restoreBtn"></span>
    </div>
    <div class="liveChatBody">
        <div id="waiting">
            <img id="loading" width="30px" height="30px" src="{{asset('packages/kodeplusdev/kandylaravel/assets/img/loading.gif')}}" title="loading">
            <p>Please wait a moment...</p>
        </div>
        <div id="registerForm">
            <form id="customerInfo" method="POST" action="/kandy/registerGuest" >
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
                <div class="rateit" data-rateit-backingfld="#backing2b"></div>
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
{{HTML::script(\Kodeplusdev\Kandylaravel\Kandylaravel::KANDY_JS)}}
{{HTML::script(\Kodeplusdev\Kandylaravel\Kandylaravel::KANDY_JS_LIVE_CHAT)}}
{{HTML::script(\Kodeplusdev\Kandylaravel\Kandylaravel::RATE_JS)}}

<script>
    //agent user id
    var agent;
    var rateData;
    $(function(){
        @if(\Session::has('kandyLiveChatUserInfo'))
            getKandyUsers();
        @else
            //default ui state
            LiveChatUI.changeState();
        @endif
        $("#liveChat #ratingForm .rateit").bind('rated', function(){
            var ri = $(this);
            rateData = rateData || {};
            rateData.rate = {id: agent.main_user_id, point: ri.rateit('value')}
        });

        $("#liveChat #ratingForm .rateit").bind('reset', function(){
            rateData = rateData || {};
            if(rateData.hasOwnProperty('rate')){
                delete rateData.rate;
            }
        });

    });
</script>