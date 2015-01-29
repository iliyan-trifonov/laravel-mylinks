<?php

class UserController extends BaseController
{

    protected $rules = [
        'login' => [
            "email" => "email|required",
            "password" => "required"
        ],
        'register' => [
            "username" => "required|min:4",
            "password" => "required|min:4|confirmed",
            "email" => "required|email"
        ]
    ];

    public function showLogin()
    {
        return View::make('links.login');
    }

    public function postLogin()
    {
        $valid = Validator::make(Input::all(), $this->rules['login']);

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
            ->with(
                "success",
                "User <i>".Auth::user()->username."</i> successfully logged in!"
            );
    }

    public function logout()
    {
        Auth::logout();
        return Redirect::route("home");
    }

    public function showRegister()
    {
        return View::make("links.register");
    }

    public function register()
    {
        $valid = Validator::make(Input::all(), $this->rules['register']);

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
    }

    public function showProfile()
    {
        return View::make("links.profile")
            ->with("user", Auth::user());
    }

    public function updateProfile()
    {

        $rules = $this->rules['register'];

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
            ->with(
                "success",
                "User information updated successfully!"
            );
    }
}
