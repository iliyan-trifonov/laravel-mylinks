@extends('links.master')

@section('content')

    @include('links.add_link_form')

    @include('links.search_form')

    @if(!$links->isEmpty())
        <table class="table">
            <tr>
                <th>Icon</th>
                <th>Title</th>
                <th>Url</th>
                <th>Created</th>
                <th>Delete</th>
            </tr>
            @foreach($links as $link)
                <tr>
                    <td>
                        @if($link->icon_url)
                            <img src="{{{ $link->icon_url }}}" width="32" height="32" />
                        @else
                            none
                        @endif
                    </td>
                    <td>{{{ $link->title }}}</td>
                    <td>{{ link_to($link->url, null, ['target' => '_blank']) }}</td>
                    <td>{{ $link->created_at}}</td>
                    <td>{{ link_to('/links/' . $link->id . '/delete', 'Delete') }}</td>
                </tr>
            @endforeach
        </table>

        <div class="col-md-4 col-md-offset-4 pagination-lg">
            {{ $links
                ->appends(["search" => Input::get("search")])
                ->links("links.paginator") }}
        </div>
    @else
        No links
    @endif

@stop
