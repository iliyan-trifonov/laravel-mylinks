<?php

class LinksController extends BaseController
{

    protected $rules = [
        "url" => ['url' => "required|url|active_url|unique:links"],
        'icon_url' => ['url' => 'required|url'],
        "search" => ['search' => "required|min:3"]
    ];
    protected $itemsPerPage = 20;

    public function home()
    {
        if (Auth::check()) {
            return View::make('links.index')
                ->with(
                    "links",
                    $this->getLinks()
                )
                ->with("search", "");
        } else {
            return View::make('links.guest');
        }
    }

    public function addLink()
    {
        $valid = Validator::make(Input::all(), $this->rules['url']);

        if ($valid->fails()) {
            return Redirect::back()
                ->withInput()
                ->with("errors", $valid->messages()->all());
        }

        $url = Input::get("url");
        if ($this->linkExists($url)) {
            return Redirect::back()
                ->withInput()
                ->with("error", "Url <i>$url</i> already exists!");
        }

        $link = new Link([
            "url" => Input::get("url")
        ]);

        $data = $this->getTitleAndIcon($link->url);

        if (isset($data['error'])) {
            return Redirect::back()
                ->with('error', $data['error']);
        }

        $link->title = $data['title'];
        $link->icon_url = $data['icon_url'];

        $link->user()->associate(Auth::user());

        $link->save();

        return Redirect::route("home")
            ->with("success", "Url <i>$url</i> added successfully!");
    }

    protected function getLinks($search = null)
    {
        $links = Auth::user()->links();
        if (!is_null($search)) {
            $links->where(function ($query) use ($search){
                $query->where("title", "LIKE", "%" . $search . "%")
                    ->orWhere("url", "LIKE", "%" . $search . "%");
            });
        }
        return $links
            ->orderBy("id", "DESC")
            ->paginate($this->itemsPerPage);
    }

    protected function getTitleAndIcon($url = '')
    {
        try {
            $curl = $this->getCurlData($url);

            if (isset($curl['error'])) {
                return ['error' => $curl['error']];
            }

            $html = new Htmldom();
            $html->load($curl['result']);

            $title = $html->find('title', 0)->innertext;

            $parsed = parse_url($url);
            $domain = $parsed["host"];
            $protocol = $parsed["scheme"];

            if ($icon = $html->find("link[rel='icon']", 0)) {
                $icon_url = $icon->href;
            } elseif ($icon = $html->find("link[rel='shortcut icon']", 0)) {
                $icon_url = $icon->href;
            } else {
                $icon_url = $protocol . "://" . $domain . "/favicon.ico";
            }
        } catch (Exception $exception) {
            return ['error' => $exception->getMessage()];
        }

        if (isset($icon_url)) {
            if (!starts_with($icon_url, "http:")
                && !starts_with($icon_url, "https:")
            ) {
                if (!starts_with($icon_url, "//")) {
                    if (!strpos($icon_url, $domain)) {
                        $icon_url = $domain . "/" . $icon_url;
                        $icon_url = str_replace("//", "/", $icon_url);
                    }
                    $icon_url = $protocol . "://" . $icon_url;
                } else {
                    $icon_url = "http:" . $icon_url;
                }
            }

            $valid = Validator::make(['url' => $icon_url], $this->rules['icon_url']);
            if ($valid->fails()) {
                $icon_url = null;
            } else {
	            $curl = $this->getCurlData($icon_url);
	            if (!isset($curl['error'])
	            	&& false !== $curl['info']
	                && 200 === $curl['info']['http_code']
	                && $curl['info']['size_download'] > 0
	            ) {
	                $icon_url = $curl['info']['url'];
	            }
	        }
        }

        return [
            'title' => $title,
            'icon_url' => $icon_url
        ];
    }

    protected function getCurlData($url = "")
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        $agent = $_SERVER['HTTP_USER_AGENT'];
        curl_setopt($curl, CURLOPT_USERAGENT, $agent);
        $result = curl_exec($curl);
        if (false === $result) {
            return ['error' => curl_error($curl)];
        }
        $info = curl_getinfo($curl);
        curl_close($curl);
        return [
            'result' => $result,
            'info' => $info
        ];
    }

    protected function linkExists($url = '')
    {
        return ! Auth::user()
            ->links()
            ->where("url", $url)
            ->get()
            ->isEmpty();
    }

    public function showDeleteLink($id)
    {
        return View::make("links.delete")
            ->with("link",
                Auth::user()
                ->links()
                ->find($id));
    }

    public function deleteLink($id)
    {
        $link = Auth::user()->links()->find($id);
        $url = $link->url;
        $link->delete();
        return Redirect::route("home")
            ->with(
                "success",
                "Link <i>$url</i> deleted successfully!"
            );
    }

    public function search()
    {
        $valid = Validator::make(Input::all(), $this->rules['search']);

        if ($valid->fails()) {
            return Redirect::back()
                ->with("errors", $valid->messages()->all());
        }

        return View::make("links.index")
            ->with("search", Input::get("search"))
            ->with("links", $this->getLinks(Input::get("search")));
    }
}
