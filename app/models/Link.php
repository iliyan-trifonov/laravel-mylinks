<?php

class Link extends Eloquent
{
    protected $fillable = ["title", "url", "icon_url"];

    public function user()
    {
        return $this->belongsTo('User');
    }
}
