<div class="{{$class}}" {{$htmlAttr}}>
    <div class="msgContainer">
        <textarea placeholder="{{$options['messageHolder']}}" name="msg" id="msg" cols="30" rows="10"></textarea>
    </div>
    <div class="sendTo">
        <input type="text" placeholder="{{$options['numberHolder']}}" name="phoneNum" id="phoneNum">
    </div>
    <button id="{{$options['btnSendId']}}">{{$options['btnSendLabel']}}</button>
    <!-- end oncall -->
</div>
<script>
    $(function(){
        $("#<?php echo $options['btnSendId'] ?>").click(function(){
            kandy_sendSms($("#phoneNum").val(),'', $("#msg").val());
        });
    })
</script>
