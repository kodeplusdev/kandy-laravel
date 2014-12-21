<div class="{{$class}}" id="{{$id}}" {{$htmlOptionAttributes}}>
    <div class="kandyVideoButton kandyVideoButtonSomeonesCalling" id="{{$incomingCall['id']}}" style="display:none">
        <label>{{$incomingCall['label']}}</label>
        <input class="btmAnswerVideoCall" type="button" value="{{$incomingCall['btnLabel']}}"
               onclick="kandy_answerVoiceCall(this)"/>
        <input class="otherPartyName" id="otherPartyName" type="hidden"/>
    </div><!--end someonesCalling -->

    <div class="kandyVideoButton kandyVideoButtonCallOut" id="{{$callOut['id']}}">
        <label>{{$callOut['label']}}</label>
        <input id="callOutUserId" type="text" value=""/>
        <input class="btnCall" id="callBtn" type="button" value="{{$callOut['btnLabel']}}" onclick="kandy_makeVoiceCall(this)"/>
    </div>
    <!--end callOut -->

    <div class="kandyVideoButton kandyVideoButtonCalling" id="{{$calling['id']}}" style="display:none">
        <label>{{$calling['label']}}</label>
        <input type="button" class="btnEndCall" value="{{$calling['btnLabel']}}" onclick="kandy_endCall(this)"/>
    </div>
    <!--end calling -->

    <div class="kandyVideoButton kandyVideoButtonOnCall" id="{{$onCall['id']}}" style="display:none">
        <label>{{$onCall['label']}}</label>
        <input class="btnEndCall" type="button" value="{{$onCall['btnLabel']}}" onclick="kandy_endCall(this)"/>
    </div>
    <!-- end oncall -->
</div>