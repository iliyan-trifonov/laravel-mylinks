<div class="row">
    <div class="col-md-4 col-md-offset-4">
        {{ Form::open(["url" => "/search", "method" => "get"]) }}
        <div class="input-group input-group-lg">
            <input type="text" name="search" value="{{ $search }}" class="form-control input-lg" placeholder="search by title or url">
            <span class="input-group-btn">
                <button class="btn btn-default btn-lg" type="submit">Search</button>
            </span>
        </div>
        {{ Form::close() }}
    </div>
</div>
<hr/>
