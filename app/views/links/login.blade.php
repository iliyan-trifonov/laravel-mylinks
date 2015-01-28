@extends('links.master')

@section('content')
    <div class="col-md-4 col-md-offset-4">
        {{ Form::open() }}
            <div class="form-group">
                {{ Form::label("email", "Email") }}
                {{ Form::text("email", null, ["class" => "form-control", "placeholder" => "email"]) }}
            </div>
            <div class="form-group">
                {{ Form::label("password", "Password") }}
                {{ Form::password("password", ["class" => "form-control"]) }}
            </div>
            {{ Form::submit("Submit", ["class" => "btn btn-default btn-lg btn-block"]) }}
        {{ Form::close() }}
    </div>
@stop
