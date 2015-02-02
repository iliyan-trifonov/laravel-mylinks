<?php

namespace MyLinks\Libs;

use DebugBar\DebugBar;

class Downloader
{

    protected $info = [];
    protected $messages = [];

    public function fetch($url)
    {
        if (!$url) {
            $this->addMessage('Invalid url!');
            return false;
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);

        $result = curl_exec($curl);
        if (false === $result) {
            $this->addMessage(curl_error($curl));
            return false;
        }

        $this->info = curl_getinfo($curl);
        if (false === $this->info
            || 200 !== $this->info['http_code']
            || $this->info['size_download'] === 0) {
            $this->addMessage('Invalid page!');
            return false;
        }

        curl_close($curl);
        return $result;
    }

    protected function addMessage($message)
    {
        if ($message) {
            $this->messages[] = $message;
        }
    }

    public function getMessages()
    {
        return $this->messages;
    }

    public function getFinalUrl()
    {
        return $this->info['url'];
    }
}
