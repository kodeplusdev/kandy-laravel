<div class="{{$class}} kandyButton" id="{{$id}}" {{$htmlOptionAttributes}}>
    <div class="kandyVideoButton kandyVideoButtonCallOut"
         id="{{$options['callOut']['id']}}">
        <label>{{$options['callOut']['label']}}</label>
        <input type="{{$options['callOut']['type']}}" id="psntCallOutNumber" class="" value="{{$options['callOut']['value'] or ''}}" placeholder="{{$options['callOut']['desc']}}" />
        <input data-container="{{$id}}" class="btnCall" id="callBtn" type="button"
               value="{{$options['callOut']['btnLabel']}}"
               onclick="kandy_make_pstn_call(this)"/>
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

    <div class="videoVoiceCallHolder">
        <span id="theirVideo" class="video"></span>
    </div>
<!-- end oncall -->
</div>