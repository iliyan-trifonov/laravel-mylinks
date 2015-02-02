@extends('links.master')

@section('content')
    <div class="col-md-4 col-md-offset-4">
        <div class="panel panel-default">
            <div class="panel-body">
                {{ Form::open() }}
                <div class="form-group">
                    {{ Form::label("email", "Email") }}
                    {{ Form::email("email", null, ["class" => "form-control", "placeholder" => "user@domain.com"]) }}
                </div>
                <div class="form-group">
                    {{ Form::label("password", "Password") }}
                    {{ Form::password("password", ["class" => "form-control"]) }}
                </div>
                {{ Form::submit("Submit", ["class" => "btn btn-default btn-lg btn-block"]) }}
                {{ Form::close() }}
            </div>
        </div>
    </div>
@stop
