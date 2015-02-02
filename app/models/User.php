<?php

namespace MyLinks\Models;

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;
use Auth;
use Hash;
use Validator;

class User extends BaseModel implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');

	protected $fillable = ["username", "password", "email"];

	protected $rules = [
		'login' => [
			"email" => "email|required",
			"password" => "required"
		],
		'register' => [
			"username" => "required|min:4|unique:users",
			"password" => "required|min:4|confirmed",
			"email" => "required|email|unique:users"
		]
	];

	public function links()
	{
		return $this->hasMany('MyLinks\Models\Link');
	}

	public function validateProfileAndUpdate($data)
	{
		$rules = $this->rules['register'];

		$passChanged = !empty($data['password'])
			|| !empty($data['password_confirmation']);

		if (!$passChanged) {
			unset($rules["password"]);
		}

		if (!empty($data['username'])
			&& Auth::user()->username === $data['username']
		) {
			unset($rules['username']);
		}

		if (!empty($data['email'])
			&& Auth::user()->email === $data['email']
		) {
			unset($rules['email']);
		}

		$valid = Validator::make($data, $rules);

		if ($valid->passes()) {
			$this->username = $data['username'];
			if ($passChanged) {
				$this->password = Hash::make($data['password']);
			}
			$this->email = $data['email'];
			$this->save();
			return true;
		}

		$this->messages = array_merge($this->messages, $valid->messages()->all());

		return false;
	}
}
