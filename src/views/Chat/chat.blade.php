<div class="row {{$class}} cd-tabs" id="{{$id}}" {{$htmlOptionsAttributes}} >
    <input type="hidden" class="kandy_current_username" value="{{$options['user']['name']}}"/>
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
        <div class="clear-fix"></div>
    </div>
    <nav>
        <ul class="cd-tabs-navigation">
        </ul>
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

    var userHoldingAttribute = "data-content";
    var activeClass = "selected";

    /**
     *  Ready
     */
    $(document).ready(function () {
        $("form.send-message").live("submit", function (e) {
            var username = $(this).attr('data-user');
            kandy_sendIm(username);
            e.preventDefault();
        });

        $('.cd-tabs-navigation a').live('click', function (event) {
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
    });// End document ready.
</script>