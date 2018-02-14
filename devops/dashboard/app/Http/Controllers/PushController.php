<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PushController extends Controller
{
    public function testResults(Request $request)
    {
        $json = $request->json();
        $state = $json->get('state');
        return response()->json($state);
    }
}
