<?php

namespace MyLinks\Models;

class Link extends BaseModel
{
    protected $fillable = ["title", "url", "icon_url"];

    protected $rules = [
        'url' => 'required|url',
        'title' => 'required',
        'icon_url' => 'required|url'
    ];

    public function user()
    {
        return $this->belongsTo('MyLinks\Models\User');
    }
}
