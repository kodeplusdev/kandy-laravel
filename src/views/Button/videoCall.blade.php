<div class="{{$class}}" id="{{$id}}" {{$htmlOptionAttributes}}>
    <div class="kandyButtonComponent kandyVideoButtonSomeonesCalling" id="{{$incomingCall['id']}}" style="display:none">
        <label>{{$incomingCall['label']}}</label>
        <input class="btmAnswerVideoCall" type="button" value="{{$incomingCall['btnLabel']}}"
               onclick="kandy_answerVideoCall(this)"/>
        <input class="otherPartyName" id="otherPartyName" type="hidden"/>
    </div><!--end someonesCalling -->

    <div class="kandyButtonComponent kandyVideoButtonCallOut" id="{{$callOut['id']}}">
        <label>{{$callOut['label']}}</label>
        <input id="callOutUserId" type="text" value=""/>
        <input class="btnCall" id="callBtn" type="button" value="{{$callOut['btnLabel']}}" onclick="kandy_makeCall(this)"/>
    </div>
    <!--end callOut -->

    <div class="kandyButtonComponent kandyVideoButtonCalling" id="{{$calling['id']}}" style="display:none">
        <label>{{$calling['label']}}</label>
        <input type="button" class="btnEndCall" value="{{$calling['btnLabel']}}" onclick="kandy_endCall(this)"/>
    </div>
    <!--end calling -->

    <div class="kandyButtonComponent kandyVideoButtonOnCall" id="{{$onCall['id']}}" style="display:none">
        <label>{{$onCall['label']}}</label>
        <input class="btnEndCall" type="button" value="{{$onCall['btnLabel']}}" onclick="kandy_endCall(this)"/>
    </div>
    <!-- end oncall -->
</div>