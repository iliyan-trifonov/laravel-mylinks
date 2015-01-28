<div class="row">
    <div class="col-lg-12">
        {{ Form::open(["url" => "/"]) }}
        <div class="input-group input-group-lg">
            <input type="text" name="url" class="form-control input-lg" placeholder="http://www.sitename.com/pagename">
            <span class="input-group-btn">
                <button class="btn btn-default btn-lg" type="submit">Add</button>
            </span>
        </div>
        {{ Form::close() }}
    </div>
</div>
<hr/>
