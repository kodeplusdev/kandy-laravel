<div class="{{$class}}">
    <span class="title">{{$title}} </span>
    <select id="{{$id}}" class="dropDown" {{$htmlOptionsAttributes}} onchange="kandy_my_status_changed($(this).val())">
        <option value="0" @if(empty($presenceStatus) || (!empty($presenceStatus) && $presenceStatus == 0)) selected @endif>Available</option>
        <option value="1" @if(!empty($presenceStatus) && $presenceStatus == 1) selected @endif>Unavailable</option>
        <option value="2" @if(!empty($presenceStatus) && $presenceStatus == 2) selected @endif>Away</option>
        <option value="3" @if(!empty($presenceStatus) && $presenceStatus == 3) selected @endif>Out To Lunch</option>
        <option value="4" @if(!empty($presenceStatus) && $presenceStatus == 4) selected @endif>Busy</option>
        <option value="5" @if(!empty($presenceStatus) && $presenceStatus == 5) selected @endif>On Vacation</option>
        <option value="6" @if(!empty($presenceStatus) && $presenceStatus == 6) selected @endif>Be Right Back</option>
    </select>
</div>
<input type="hidden" id="full_user_id" value="{{$fullUserId}}">