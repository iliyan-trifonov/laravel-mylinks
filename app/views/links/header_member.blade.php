<div class="text-right">
    Logged in as {{ Auth::user()->username }}
    @if (Route::is("home"))
        <strong>Home</strong>
    @else
        {{ link_to('/', 'Home') }}
    @endif
    |
    @if (Route::is("profile"))
        <strong>Profile</strong>
    @else
        {{ link_to('/profile', 'Profile') }}
    @endif
    |
    {{ link_to('/logout', 'Logout') }}
</div>
