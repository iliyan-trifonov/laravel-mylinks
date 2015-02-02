<?php

namespace MyLinks\Controllers;

use Auth;
use Htmldom;
use Input;
use Redirect;
use Symfony\Component\Console\Input\InputOption;
use Validator;
use View;
use MyLinks\Models\Link;
use MyLinks\Libs\HtmlParser;

class LinksController extends \BaseController
{

    protected $rules = [
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
        $link = new Link([
            'url' => Input::get('url')
        ]);

        if (!$link->validate(Input::all(), 'url')) {
            return Redirect::back()
                ->withInput()
                ->with("errors", $link->getMessages());
        }

        if ($this->userLinkExists($link->url)) {
            return Redirect::back()
                ->withInput()
                ->with('error', 'Url ' . $link->url . ' already added!');
        }

        $parser = new HtmlParser();
        $data = $parser->getTitleAndIcon($link->url);

        if (!$data) {
            return Redirect::back()
                ->withInput()
                ->with('errors', $parser->getMessages());
        }

        $link->url = $data['url'];
        $link->title = $data['title'];
        $link->icon_url = $data['icon_url'];

        $link->user()->associate(Auth::user());

        $link->save();

        return Redirect::route("home")
            ->with("success", "Url ".$link->url." added successfully!");
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
                "Url $url deleted successfully!"
            );
    }

    protected function userLinkExists($url)
    {
        return Auth::user()
            ->links()
            ->where('url', $url)->count() > 0;
    }
}
