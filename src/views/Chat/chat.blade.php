<div class="row {{$class}} cd-tabs" id="{{$id}}" {{$htmlOptionsAttributes}} >
    <input type="hidden" class="kandy_current_username" value="{{ $displayName }}"/>
    <input type="hidden" class="kandy_user" value="{{ $kandyUser }}"/>

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
        <a href="#" class="button tiny right modalToggle" data-reveal-id="myModal">Create group</a>

        <div class="clear-fix"></div>
    </div>
    <nav>
        <ul class="cd-tabs-navigation contacts">
        </ul>
        <div class="separator hide"><span>Groups</span></div>
        <ul class="cd-tabs-navigation groups"></ul>
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
    var groupSeparator = '#' + wrapDivId + ' .separator';
    // group chat vars
    var displayNames = [];
    var groupNames = [];
    var usersStatus = {};
    //session listeners
    var listeners = {
              'onData': kandy_onSessionData,
              'onUserJoinRequest': kandy_onJoinRequest,
              'onUserJoin': kandy_onJoin,
              'onJoin': kandy_onJoin,
              'onUserLeave': kandy_onLeaveGroup,
              'onLeave': kandy_onLeaveGroup,
              'onUserBoot': kandy_onUserBoot,
              'onBoot': kandy_onUserBoot,
//              'onActive': kandy_onActiveGroup,
//              'onInactive': onInactive,
              'onTermination': kandy_onTerminateGroup
              'onJoinApprove': kandy_onJoinApprove,
              'onJoinReject' : kandy_onJoinReject
            };
    var sessionListeners = [];

    /**
     *  Ready
     */
    $(document).ready(function () {
        $("form.send-message").live("submit", function (e) {
            var username = $(this).attr('data-user');
            if($(this).is('[data-user]')){
                kandy_sendIm(username);
            }else{
                kandy_sendGroupIm($(this).data('group'),$(this).find('.imMessageToSend').val());
                $(this).find('.imMessageToSend').val('');
            }
            e.preventDefault();
        });

         $('.list-users li .remove').live('click', function(e){
                var userId = $(this).parent().data('user');
                var groupId = $(this).closest('[data-group]').data('group');
                kandy_removeFromGroup(groupId,userId);
           });

        $('.cd-tabs-navigation > li > a').live('click', function (event) {
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

        $(".toggle").live('click',function(){
            $(this).toggleClass('fa-plus-square-o').toggleClass('fa-minus-square-o');
            $(this).siblings('.list-users').toggleClass('expanding');
        })

    });// End document ready.
</script>