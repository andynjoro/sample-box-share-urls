<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AmazonAutocomplete extends Controller {
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request) {
        $api_request = 'https://completion.amazon.com/api/2017/suggestions?mid=ATVPDKIKX0DER&alias=aps&prefix='.$request->input('query');

        $api_response = Http::get($api_request);

        if ($api_response->successful()) {
            $data = $api_response['suggestions'];

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        }

        return response()->json([
            'error' => true,
        ]);
    }
}
