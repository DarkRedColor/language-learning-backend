

<h1 id="adventure-h">Adventures</h1>
<div id="filter" class="field">
    <label>Filter</label>
    <div class="ui selection dropdown">
        <input type="hidden" name="filter" value="all">
        <div class="text">All</div>
        <div class="menu">
            <div class="item default" data-value="all">All</div>
            <div class="item" data-value="unlock">Unlock</div>
        </div>
    </div>
</div>
<div class="ui small breadcrumb">
    <a href="#" ><i class="arrow left icon"></i></a>
    <a href="#" class="section">Adventure</a>
    <a href="#" class="section"><i class="right chevron icon divider"></i>Season 1</a>
    <a href="#" class="section"><i class="right chevron icon divider"></i>Episode 2</a>
    {{--    if it will be selected--}}
    <a href="#" class="active section"><i class="right chevron icon divider"></i>Chapter 2</a>

</div>
<div class="ui search s-adv">
    <div class="ui icon input">
        <input class="prompt" type="text" placeholder="Type something">
        <i class="search icon"></i>
    </div>
</div>
<div class="ui buttons fluid">
    <div class="ui left labeled button season adv check">
        <a href="#" class="ui basic label">
            <span>S1</span><p>Season name</p>
        </a>
        <button class="ui icon button">
            <i class="edit outline icon"></i>
        </button>
        <button class="un-lock ui icon button">
            <i class="unlock alternate icon"></i>
        </button>
    </div>
    <div class="ui left labeled button chapter adv">
        <a href="#" class="ui basic label">
            <span>C2</span><p>Chapter name</p>
        </a>
        <button class="ui icon button">
            <i class="edit outline icon"></i>
        </button>
        <button class="lock ui icon button">
            <i class="lock alternate icon"></i>
        </button>
    </div>
    <div class="ui left labeled button quiz adv">
        <a href="#" class="ui basic label">
            <span>Q1</span><p>Quiz</p>
        </a>
        <button class="ui icon button">
            <i class="edit outline icon"></i>
        </button>
        <button class="lock ui icon button">
            <i class="lock alternate icon"></i>
        </button>
    </div>
    <button class="add ui basic button"><i class="plus icon"></i>add new season</button>
</div>