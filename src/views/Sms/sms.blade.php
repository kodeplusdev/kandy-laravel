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
    jQuery(function(){
        jQuery("#<?php echo $options['btnSendId'] ?>").click(function(){
            kandy_sendSms(jQuery("#phoneNum").val(),'', jQuery("#msg").val());
        });
    });
</script>
