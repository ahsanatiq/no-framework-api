<?php
namespace App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as LaravelCollection;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

abstract class BaseController
{
    public function toFractalResponse($data, $transformer, LengthAwarePaginator $paginator = null)
    {
        $fractal = new Manager();
        if ($data instanceof LaravelCollection) {
            $resource = new Collection($data, $transformer);
            if ($paginator && $data->count()) {
                $paginator->withPath($this->stripPageAndPortFromUrl(currentUrl())->getUrl());
                $resource->setPaginator(new IlluminatePaginatorAdapter($paginator));
            }
        } else {
            $resource = new Item($data, $transformer);
        }
        return $fractal->createData($resource)->toJson();
    }

    public function getPageNum(Request $request)
    {
        $page_param = config()->get('app.page_param', 'page');

        return !empty($request->has($page_param))
        ? (int) $request->input($page_param)
        : 1;
    }


    public function getPerPage(Request $request)
    {
        $per_page_param = config()->get('app.per_page_param', 'per_page');

        return !empty($request->has($per_page_param))
        ? (int) $request->input($per_page_param)
        : (int) config()->get('app.items_per_page');
    }

    public function stripPageAndPortFromUrl($url)
    {
        $appUrl = \Purl\Url::parse(config()->get('app.url'));
        $url->set('host', $appUrl->get('host'));
        $url->set('port', $appUrl->get('port'));
        $url->set('scheme', $appUrl->get('scheme'));
        if (!empty($url->get('port')) && $url->get('port') == '80') {
            $url->set('port', '');
        }
        if (!empty($url->query->get('page'))) {
            $url->query->remove('page');
        }
        return $url;
    }
}
