<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cache;

class PushControllerJson extends Controller
{
    public function testResultsAcceptance(Request $request)
    {
        $json = $request->json();
        $state = $json->get('state');

        $value = json_decode(response()->json($state)->content());

        Cache::put('test_results_acceptance', $value, 7 * 24 * 60);

        return response()->json($value);
    }

}
