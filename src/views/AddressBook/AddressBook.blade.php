<div class="{{$class}}" id="{{$id}}" {{$htmlOptionsAttributes}}>
    <div class="kandyAddressContactList">
        <div class="myContactsTitle"><p>{{$title}}</p></div>
    </div>

    <form class="kandyDirectorySearch" onsubmit="return false;">
        User: <input id="kandySearchUserName" type="text" value=""/>
        <input type="submit" value="Search" onclick="kandy_searchDirectoryByUserName();return false;"/>
        (asterisk for wildcard)
    </form>

    <div class="kandyDirSearchResults" id="dirSearchResults">
        <div class="kandyDirSearchTitle">Directory Search Results</div>
    </div>
</div>