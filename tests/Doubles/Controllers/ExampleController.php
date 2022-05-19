<?php

namespace Worksome\RequestFactories\Tests\Doubles\Controllers;

use App\Http\Requests\ExampleFormRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

final class ExampleController extends Controller
{
    public function __invoke(ExampleFormRequest $request)
    {
        return response()->json(array_merge(
            $request->input(),
            ['files' => array_keys($request->allFiles())],
        ));
    }

    public function store(Request $request)
    {
        return response()->json(array_merge(
            $request->input(),
            ['files' => array_keys($request->allFiles())],
        ));
    }
}
