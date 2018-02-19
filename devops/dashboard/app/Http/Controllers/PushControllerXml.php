<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cache;

class PushControllerXml extends Controller
{
    public function testResultsUnit(Request $request)
    {
        $xml = $request->xml();

        $value = $xml['testsuite']['@attributes'];

        Cache::put('test_results_unit', $value, 7 * 24 * 60);

        return $value;
    }
}
