<?php

namespace MyLinks\Libs;

use MyLinks\Models\Link;
use Psr\Log\InvalidArgumentException;

class HtmlParser
{

    protected $downloader;
    protected $messages = [];

    public function __construct()
    {
        $this->downloader = new Downloader();
    }

    public function getTitleAndIcon($url = null)
    {
        if (is_null($url)) {
            $this->addMessage('No url specified!');
            return false;
        }

        $fetched = $this->downloader->fetch($url);

        if (!$fetched) {
            $this->addMessage($this->downloader->getMessages());
            return false;
        }

        $urlFinal = $this->downloader->getFinalUrl();

        $html = new \Htmldom();

        try {
            $html->load($fetched);
        } catch (\Exception $exception) {
            $this->addMessage($exception->getMessage());
            return false;
        }

        $title = $this->getTitle($html);

        if (!$title) {
            $this->addMessage('Could not get the Title!');
            return false;
        }

        $icon_url = $this->getIcon($html, $url);

        return [
            'url' => $urlFinal,
            'title' => $title,
            'icon_url' => $icon_url
        ];
    }

    protected function getTitle($html)
    {
        try {
            $title = $html->find('title', 0)->innertext;
        } catch (\Exception $exception) {
            $this->addMessage($exception->getMessage());
            return false;
        }
        return $title;
    }

    protected function getIcon($html, $url)
    {

        $props = $this->getDomainProps($url);

        $iconUrl = $this->buildIconUrl($html, $props);

        $link = new Link([
            'icon_url' => $iconUrl
        ]);

        if (!$link->validate(['icon_url' => $iconUrl], 'icon_url')) {
            $iconUrl = null;
        } elseif ($this->downloader->fetch($iconUrl)) {
            $iconUrl = $this->downloader->GetFinalUrl();
        } else {
            $iconUrl = null;
        }

        return $iconUrl;
    }

    protected function getDomainProps($url)
    {
        $parsed = parse_url($url);
        $domain = $parsed["host"];
        $protocol = $parsed["scheme"];
        return [
            'domain' => $domain,
            'protocol' => $protocol
        ];
    }

    protected function buildIconUrl($html, $props)
    {
        if ($icon = $html->find("link[rel='icon']", 0)) {
            $iconUrl = $icon->href;
        } elseif ($icon = $html->find("link[rel='shortcut icon']", 0)) {
            $iconUrl = $icon->href;
        } else {
            $iconUrl = $props['protocol'] . "://" . $props['domain'] . "/favicon.ico";
        }

        // data:image:base64
        if (starts_with($iconUrl, 'data:')) {
            $iconUrl = null;
        } elseif (starts_with($iconUrl, '//')) {
            // //domain.com/favicon.ico
            $iconUrl = $props['protocol'] . ':' . $iconUrl;
        } else {
            if (!starts_with($iconUrl, "http:")
                && !starts_with($iconUrl, "https:")
            ) {
                // /favicon.ico
                if (!strpos($iconUrl, $props['domain'])) {
                    $iconUrl = $props['domain'] . '/' . $iconUrl;
                    $iconUrl = str_replace('//', '/', $iconUrl);
                }
                $iconUrl = $props['protocol'] . '://' . $iconUrl;
            }
        }

        return $iconUrl;
    }

    protected function addMessage($message)
    {
        if (is_array($message)) {
            $this->messages = array_merge($this->messages, $message);
        } elseif (is_string($message)) {
            $this->messages[] = $message;
        } else {
            throw new InvalidArgumentException('Invalid message type!');
        }
    }

    public function getMessages()
    {
        return $this->messages;
    }
}
