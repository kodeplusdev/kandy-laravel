<div class="{{$class}}">
    <span class="title">{{$title}} </span>
    <select id="{{$id}}" class="dropDown" {{$htmlOptionsAttributes}} onchange="kandy_myStatusChanged($(this).val())">
        <option value="0" selected>Available</option>
        <option value="1">Unavailable</option>
        <option value="2">Away</option>
        <option value="3">Out To Lunch</option>
        <option value="4">Busy</option>
        <option value="5">On Vacation</option>
        <option value="6">Be Right Back</option>
    </select>
</div>