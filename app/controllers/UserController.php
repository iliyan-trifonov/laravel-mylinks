<?php

namespace MyLinks\Controllers;

use Auth;
use Hash;
use Input;
use Redirect;
use Validator;
use View;
use MyLinks\Models\User as User;

class UserController extends \BaseController
{
    public function showLogin()
    {
        return View::make('links.login');
    }

    public function postLogin()
    {

        $credentials = Input::only('email', 'password');

        $user = new User($credentials);

        if (!$user->validate($credentials, 'login')) {
            return Redirect::back()
                ->withInput()
                ->with("errors", $user->getMessages());
        }

        if (!Auth::attempt($credentials)) {
            return Redirect::back()
                ->withInput()
                ->with("error", "Invalid email or password!");
        }

        return Redirect::route("home")
            ->with(
                "success",
                "User ".Auth::user()->username." successfully logged in!"
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

        $credentials = Input::all();

        $user = new User($credentials);

        if (!$user->validate($credentials, 'register')) {
            return Redirect::back()
                ->withInput()
                ->with("errors", $user->getMessages());
        }

        $user->save();

        Auth::login($user);

        return Redirect::route("home")
            ->with(
                'success',
                'User ' . $user->username . ' registered successfully!'
            );
    }

    public function showProfile()
    {
        return View::make('links.profile')
            ->with('user', Auth::user());
    }

    public function updateProfile()
    {
        $user = Auth::user();

        if ( ! $user->validateProfileAndUpdate(Input::all())) {
            return Redirect::back()
                ->withInput()
                ->with('errors', $user->getMessages());
        }

        return Redirect::route('profile')
            ->with(
                'success',
                'User information updated successfully!'
            );
    }
}
