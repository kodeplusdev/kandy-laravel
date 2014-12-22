<div class="{{$class}}" id="{{$id}}" {{$htmlOptionsAttributes}}>
    <input type="hidden" class="kandy_current_username" value="{{$options['user']['name']}}" />
    <form id="imForm" onsubmit="return false;">
        <div id="{{$options['contact']['id']}}">
            {{$options['contact']['label']}}: <select class="imToContact"/></select>
        </div>
        <div id="{{$options['message']['id']}}">
            {{$options['message']['label']}}: <input class= "imMessageToSend" type="text" />
        </div>

        <input class="btnSendMessage" type="submit" onclick="kandy_sendIm();return false;" value="Send"/>  <br/>
    </form>
    <div class="kandyMessages"></div>
</div>