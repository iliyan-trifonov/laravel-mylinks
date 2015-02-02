<?php

namespace MyLinks\Models;

class BaseModel extends \Eloquent
{

    protected $rules = [];
    protected $messages = [];

    public function validate($data, $rule = null)
    {

        if (empty($this->rules)) {
            $this->messages[] = 'No rules specified!';
            return false;
        }

        $rules = $this->rules;

        if (!is_null($rule) && isset($rules[$rule])) {
            if (is_array($rules[$rule])) {
                $rules = $rules[$rule];
            } else {
                $rules = [$rule => $rules[$rule]];
            }
        }

        $valid = \Validator::make($data, $rules);

        if ($valid->passes()) {
            return true;
        }

        $this->messages = $valid->messages()->all();

        return false;
    }

    public function getMessages()
    {
        return $this->messages;
    }

    public function getRules($rule = null)
    {
        if ( ! is_null($rule) && isset($this->rules[$rule])) {
            return $this->rules[$rule];
        }
        return $this->rules;
    }
}
