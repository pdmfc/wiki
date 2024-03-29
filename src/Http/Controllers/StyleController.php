<?php

namespace Pdmfc\Wiki\Http\Controllers;

use Illuminate\Http\Request;
use Pdmfc\Wiki\LaRecipe;
use Pdmfc\Wiki\Wiki;

class StyleController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        return response(
            file_get_contents(Wiki::allStyles()[$request->style]),
            200, ['Content-Type' => 'text/css']
        );
    }
}
