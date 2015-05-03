<?php

use MyLinks\Models\User;

class UserTableSeeder extends DatabaseSeeder
{
    public function run()
    {
        User::create([
            "username" => "iliyan",
            "password" => Hash::make("1234"),
            "email" => "iliyan@host.com"
        ]);
    }
}
