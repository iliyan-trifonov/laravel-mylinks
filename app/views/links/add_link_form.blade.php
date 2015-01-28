<div class="row">
    <div class="col-lg-12">
        {{ Form::open(["url" => "/"]) }}
        <div class="input-group input-group-lg">
            {{ Form::text("url", null,
                ["class" => "form-control input-lg",
                "placeholder" => "http://www.sitename.com/pagename"]) }}
            <span class="input-group-btn">
                {{ Form::submit("Add", ["class" => "btn btn-default btn-lg"]) }}
            </span>
        </div>
        {{ Form::close() }}
    </div>
</div>
<hr/>
