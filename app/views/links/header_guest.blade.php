<div class="text-right">
    @if (Route::is("home"))
        <strong>Home</strong>
    @else
        {{ link_to('/', 'Home') }}
    @endif
    |
    @if (Route::is("login"))
        <strong>Log in</strong>
    @else
        {{ link_to('/login', 'Log in') }}
    @endif
    |
    @if (Route::is("register"))
        <strong>Register</strong>
    @else
        {{ link_to('/register', 'Register') }}
    @endif
</div>
