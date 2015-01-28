@extends('links.master')

@section('content')
    {{ Form::open(["method" => "delete"]) }}
        Delete link "{{ $link->title }}"?
        {{ Form::submit("Delete", ["class" => "btn btn-default"]) }}
    {{ Form::close() }}
@stop
