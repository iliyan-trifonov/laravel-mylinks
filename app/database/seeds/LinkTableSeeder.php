<?php

use MyLinks\Models\User;
use MyLinks\Models\Link;

class LinkTableSeeder extends DatabaseSeeder
{
    public function run()
    {
        $user = User::find(1);

        $link = new Link([
            "title" => "Laravel Forum",
            "icon_url" => "http://laravel.io/favicon.ico",
            "url" => "http://laravel.io/forum"
        ]);

        $user->links()->save($link);
    }
}
