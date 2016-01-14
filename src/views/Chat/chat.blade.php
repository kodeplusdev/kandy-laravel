<?php $userParts = explode("@", $kandyUser) ?>
<div class="row {{$class}} cd-tabs" id="{{$id}}" {{$htmlOptionsAttributes}} >
    <input type="hidden" class="kandy_current_username" value="{{ $displayName }}"/>
    <input type="hidden" class="kandy_user" value="{{ $kandyUser }}"/>
    <input type="hidden" class="kandy_domain_name" value="{{$userParts[1]}}">

    <div class="chat-heading">
        <div class="contact-heading">
            <label>Contacts:</label>
            <select onchange="kandy_contactFilterChanged($(this).val())">
                <option value="all">All</option>
                <option value="offline">Offline</option>
                <option value="available">Available</option>
                <option value="unavailable">Unavailable</option>
                <option value="away">Away</option>
                <option value="out-to-lunch">Out To Lunch</option>
                <option value="busy">Busy</option>
                <option value="on-vacation">On Vacation</option>
                <option value="be-right-back">Be Right Back</option>
            </select>
        </div>
        <div class="chat-with-message">
            Chatting with <span class="chat-friend-name"></span>
        </div>
        <a href="javascript:;" class="button tiny right modalToggle" data-reveal-id="myModal">Create Group</a>

        <div class="clear-fix"></div>
    </div>
    <nav>
        <ul class="cd-tabs-navigation contacts">
        </ul>
        <div class="separator hide group"><span>Groups</span></div>
        <ul class="cd-tabs-navigation groups"></ul>
        <div class="separator hide livechatgroup"><span>Live Chat</span></div>
        <ul class="cd-tabs-navigation livechats "></ul>
    </nav>

    <ul class="cd-tabs-content">
    </ul>

    <div style="clear: both;"></div>
</div>

<script>

    var wrapDivId = "{{ $id }}";
    var liTabWrapClass = "cd-tabs-navigation";
    var liContentWrapClass = "cd-tabs-content";
    var liTabWrapSelector = "#" + wrapDivId + " ." + liTabWrapClass;
    var liContentWrapSelector = "#" + wrapDivId + " ." + liContentWrapClass;
    var tabContentWrapper = $(liContentWrapSelector);
    var listUserClass = 'list-users';
    var userHoldingAttribute = "data-content";
    var activeClass = "selected";
    var liTabGroupsWrap = liTabWrapSelector + '.groups';
    var liTabContactWrap = liTabWrapSelector + '.contacts';
    var liTabLiveChatWrap = liTabWrapSelector + '.livechats';
    var groupSeparator = '#' + wrapDivId + ' .separator.group';
    var liveChatGroupSeparator = '#' + wrapDivId + ' .separator.livechatgroup';
    // group chat vars
    var displayNames = [];
    var groupNames = [];
    var usersStatus = {};
    //session listeners
    var listeners = {
        chatGroupMessage: kandy_onGroupMessage,
        chatGroupInvite: '',
        chatGroupBoot: '',
        chatGroupLeave: '',
        chatGroupUpdate: '',
        chatGroupDelete: ''
    };
    var sessionListeners = [];

    /**
     *  Ready
     */
    $(document).ready(function () {
        heartBeat(60000);
        $(window).bind('beforeunload', kandy_updateUserStatus);
        $(document).on("submit", "form.send-message", function (e) {
            var username = $(this).attr('data-user');
            var realID = jQuery(this).data('real-id');
            if(realID == ''){
                realID = username;
            }
            e.preventDefault();
            if($(this).is('[data-user]')){
                kandy_sendIm(realID,username);
            }else{
                kandy_sendGroupIm($(this).data('group'),$(this).find('.imMessageToSend').val());
                $(this).find('.imMessageToSend').val('');
            }
        });

         $(document).on('click', '.list-users li .remove', function(e){
                var userId = $(this).closest('li').data('user');
                var groupId = $(this).closest('[data-group]').data('group');
                kandy_removeFromGroup(groupId,userId);
           });

        $(document).on('click', '.cd-tabs-navigation > li > a', function (event) {
            event.preventDefault();
            var selectedItem = $(this);
            if (!selectedItem.hasClass('selected')) {
                var selectedTab = selectedItem.data('content'),
                    selectedContent = tabContentWrapper.find('li[data-content="' + selectedTab + '"]'),
                    selectedContentHeight = selectedContent.innerHeight();

                $('.cd-tabs-navigation a').removeClass('selected');
                selectedItem.addClass('selected');
                selectedContent.addClass('selected').siblings('li').removeClass('selected');

                // Set focus
                selectedContent.find(".imMessageToSend").focus();
                $(this).parent().find('.toggle').trigger('click');

                // Set chat heading
                $(".chat-with-message").show();
                $(".chat-friend-name").html(selectedItem.html());

                //animate tabContentWrapper height when content changes
                tabContentWrapper.animate({
                    'height': selectedContentHeight
                }, 200);
            }
        });

        //hide the .cd-tabs::after element when tabbed navigation has scrolled to the end (mobile version)
        checkScrolling($('.cd-tabs nav'));

        $(window).on('resize', function () {
            checkScrolling($('.cd-tabs nav'));
        });

        $('.cd-tabs nav').on('scroll', function () {
            checkScrolling($(this));
        });

        function checkScrolling(tabs) {
            var totalTabWidth = parseInt(tabs.children('.cd-tabs-navigation').width()),
                tabsViewPort = parseInt(tabs.width());
            if (tabs.scrollLeft() >= totalTabWidth - tabsViewPort) {
                tabs.parent('.cd-tabs').addClass('is-ended');
            } else {
                tabs.parent('.cd-tabs').removeClass('is-ended');
            }
        }

        $(document).on('click', ".toggle", function(){
            $(this).toggleClass('fa-plus-square-o').toggleClass('fa-minus-square-o');
            $(this).siblings('.list-users').toggleClass('expanding');
        });
    });// End document ready.
</script>

