<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', ['as' => 'home', function () {
    if (Auth::check()) {
        return View::make('links.index')
            ->with(
                "links",
                Auth::user()
                    ->links()
                    ->orderBy("id", "DESC")
                    ->paginate(5)
            )
            ->with("search", "");
    } else {
        return View::make('links.guest');
    }
}]);

Route::post('/', function () {
    $rules = ["url" => "required|url"];
    $valid = Validator::make(Input::all(), $rules);

    if ($valid->fails()) {
        return Redirect::back()
            ->withInput()
            ->with("errors", $valid->messages()->all());
    }

    $url = Input::get("url");
    if (!Auth::user()->links()->where("url", $url)
        ->get()
        ->isEmpty()
    ) {
        return Redirect::back()
            ->withInput()
            ->with("error", "Url <i>$url</i> already exists!");
    }

    $link = new Link([
        "url" => Input::get("url")
    ]);

    try {

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $link->url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        //$agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)';
        $agent = $_SERVER['HTTP_USER_AGENT'];
        curl_setopt($curl, CURLOPT_USERAGENT, $agent);
        $str = curl_exec($curl);
        curl_close($curl);

        $html = new Htmldom();
        $html->load($str);

        $link->title = $html->find('title', 0)->innertext;

        $parsed = parse_url($link->url);
        $domain = $parsed["host"];
        $protocol = $parsed["scheme"];

        if ($icon = $html->find("link[rel='icon']", 0)) {
            $link->icon_url = $icon->href;
        } elseif ($icon = $html->find("link[rel='shortcut icon']", 0)) {
            $link->icon_url = $icon->href;
        } else {
            $link->icon_url = $protocol . "://" . $domain . "/favicon.ico";
        }

    } catch (ErrorException $exception) {
        return Redirect::back()
            ->withInput()
            ->with("error", $exception->getMessage());
    }

    if (isset($link->icon_url)) {
        if (!starts_with($link->icon_url, "http:")
            && !starts_with($link->icon_url, "https:")
        ) {
            if (!starts_with($link->icon_url, "//")) {
                if (!strpos($link->icon_url, $domain)) {
                    $link->icon_url = $domain . "/" . $link->icon_url;
                    $link->icon_url = str_replace("//", "/", $link->icon_url);
                }
                $link->icon_url = $protocol . "://" . $link->icon_url;
            } else {
                //$link->icon_url = $protocol . ":" . $link->icon_url;
                $link->icon_url = "http:" . $link->icon_url;
            }
        }
    }

    //$file_headers = @get_headers($link->icon_url);
    /*if (!$file_headers || strpos($file_headers[0], "200 OK") === false) {
        unset($link->icon_url);
    }*/

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $link->icon_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_exec($curl);
    $info = curl_getinfo($curl);
    curl_close($curl);

    if (!$info || $info["http_code"] !== 200 || !$info["size_download"]) {
        unset($link->icon_url);
    } else {
        $link->icon_url = $info['url'];
    }

    $link->user()->associate(Auth::user());

    $link->save();

    return Redirect::route("home")
        ->with("success", "Url <i>$url</i> added successfully!");
});

Route::get('/login', ['as' => 'login', function () {
    return View::make('links.login');
}]);

Route::post('/login', function () {
    $rules = [
        "email" => "email|required",
        "password" => "required"
    ];

    $valid = Validator::make(Input::all(), $rules);

    if ($valid->fails()) {
        return Redirect::back()
            ->withInput()
            ->with("errors", $valid->messages()->all());
    }

    if (!Auth::attempt(Input::only("email", "password"))) {
        return Redirect::back()
            ->withInput()
            ->with("error", "Invalid email or password!");
    }

    return Redirect::to("/")
        ->with("success", "User successfully logged in!");
});

Route::get("/links/{id}/delete", function ($id) {
    return View::make("links.delete")
        ->with("link", Auth::user()->links()->find($id));
});

Route::delete("/links/{id}/delete", function ($id) {
    $link = Auth::user()->links()->find($id);
    $url = $link->url;
    $link->delete();
    return Redirect::route("home")
        ->with("success", "Link <i>$url</i> deleted successfully!");
});

Route::get("/logout", function () {
    Auth::logout();
    return Redirect::route("home");
});

Route::get("/register", ["as" => "register", function () {
    return View::make("links.register");
}]);

Route::post("/register", function () {
    $rules = [
        "username" => "required|min:4",
        "password" => "required|min:4|confirmed",
        "email" => "required|email"
    ];

    $valid = Validator::make(Input::all(), $rules);

    if ($valid->fails()) {
        return Redirect::back()
            ->withInput()
            ->with("errors", $valid->messages()->all());
    }

    if (!User::where("username", Input::get("username"))
        ->orWhere("email", Input::get("email"))->get()->isEmpty()
    ) {
        return Redirect::back()
            ->withInput()
            ->with("error", "Username or email already exists!");
    }

    $user = User::create([
        "username" => Input::get("username"),
        "password" => Hash::make(Input::get("password")),
        "email" => Input::get("email")
    ]);

    Auth::login($user);

    return Redirect::route("home");
});

Route::get("/search", function () {
    return View::make("links.index")
        ->with("search", Input::get("search"))
        ->with("links", Auth::user()
            ->links()
            ->where(function ($query) {
                $query->where("title", "LIKE", "%" . Input::get("search") . "%")
                    ->orWhere("url", "LIKE", "%" . Input::get("search") . "%");
            })
            ->paginate(5));
});

Route::get("/profile", ["as" => "profile", function(){
    return View::make("links.profile")
        ->with("user", Auth::user());
}]);

Route::post("/profile", function(){
    $rules = [
        "username" => "required|min:4",
        "password" => "required|min:4:confirmed",
        "email" => "required|email"
    ];

    $checkPass = Input::has("password") || Input::has("password_confirmation");

    if (!$checkPass) {
        unset($rules["password"]);
    }

    $valid = Validator::make(Input::all(), $rules);

    if ($valid->fails()) {
        return Redirect::back()
            ->withInput()
            ->with("errors", $valid->messages()->all());
    }

    $user = Auth::user();

    $user->username = Input::get("username");
    if ($checkPass) {
        $user->password = Hash::make(Input::get("password"));
    }
    $user->email = Input::get("email");

    $user->save();

    return Redirect::route("profile")
        ->with("success", "User information updated successfully!");
});
