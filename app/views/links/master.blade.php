<!doctype html>
<html>
    <head>
        <meta lang="en" />
        <title>My Links</title>
        <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.min.css') }}" />
    </head>
    <body>
        <div class="container">

            <div class="page-header">

                <div class="pull-left">
                    <strong>
                        My Links ::
                        @if(Route::is("search"))
                            Search
                        @elseif(Route::is("profile"))
                            Profile
                        @else
                            Home
                        @endif
                    </strong>
                </div>

                @if (Auth::check())
                    @include('links.header_member')
                @else
                    @include('links.header_guest')
                @endif
            </div>

            @if(Session::has("success"))
                <div class="alert alert-success">{{ Session::get("success") }}</div>
            @endif

            @if(Session::has("error"))
                <div class="alert alert-danger">{{ Session::get("error") }}</div>
            @endif

            @if(Session::has("errors"))
                @foreach(Session::get("errors") as $error)
                    <div class="alert alert-danger">{{ $error }}</div>
                @endforeach
            @endif

            @yield('content')

        </div>
    </body>
</html>
