<div class="{{$class}}" id="{{$id}}" {{$htmlOptionAttributes}}>
<div class="kandyVideoButton kandyVideoButtonSomeonesCalling"
     id="{{$options['incomingCall']['id']}}">
    <label>{{$options['incomingCall']['label']}}</label>
    <input data-container="{{$id}}" class="btmAnswerVideoCall" type="button"
           value="{{$options['incomingCall']['btnLabel']}}"
           onclick="kandy_answer_video_call(this)"/>
</div><!--end someonesCalling -->

<div class="kandyVideoButton kandyVideoButtonCallOut"
     id="{{$options['callOut']['id']}}">
    <label>{{$options['callOut']['label']}}</label>
    <input id="callOutUserId" class="select2" />
    <input data-container="{{$id}}" class="btnCall" id="callBtn" type="button"
           value="{{$options['callOut']['btnLabel']}}"
           onclick="kandy_make_video_call(this)"/>
</div>
<!--end callOut -->

<div class="kandyVideoButton kandyVideoButtonCalling"
     id="{{$options['calling']['id']}}">
    <label>{{$options['calling']['label']}}</label>
    <input data-container="{{$id}}" type="button" class="btnEndCall"
           value="{{$options['calling']['btnLabel']}}"
           onclick="kandy_end_call(this)"/>
</div>
<!--end calling -->

<div class="kandyVideoButton kandyVideoButtonOnCall"
     id="{{$options['onCall']['id']}}">
    <label>{{$options['onCall']['label']}}</label>
    <input data-container="{{$id}}" class="btnEndCall" type="button"
           value="{{$options['onCall']['btnLabel']}}"
           onclick="kandy_end_call(this)"/>
</div>
<!-- end oncall -->
</div>