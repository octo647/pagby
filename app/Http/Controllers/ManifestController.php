<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ManifestController extends Controller
{
    public function __invoke(Request $request)
    {
        $tenant = tenant();
        $logo = $tenant && $tenant->logo ? $tenant->logo : asset('icons/icon-192x192.png');
        $name = $tenant && $tenant->fantasy_name ? $tenant->fantasy_name : config('app.name', 'Pagby');
        $shortName = mb_strimwidth($name, 0, 12, '');
        $manifest = [
            'name' => $name,
            'short_name' => $shortName,
            'start_url' => '/',
            'display' => 'standalone',
            'background_color' => '#111111',
            'theme_color' => '#111111',
            'icons' => [
                [
                    'src' => $logo,
                    'sizes' => '192x192',
                    'type' => 'image/png',
                ],
                [
                    'src' => $logo,
                    'sizes' => '512x512',
                    'type' => 'image/png',
                ],
            ],
        ];
        return response()->json($manifest);
    }
}
