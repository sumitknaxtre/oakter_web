<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = [
            route('website.home'),
            route('website.about'),
            route('website.contact'),
            route('website.privacy'),
            route('website.company_policies'),
            route('website.media_insights'),
            route('website.mini_ups'),
            route('website.collections.all'),
            route('website.retail_outlets'),
        ];

        $body = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $body .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($urls as $loc) {
            $body .= "  <url>\n";
            $body .= '    <loc>'.e($loc)."</loc>\n";
            $body .= "  </url>\n";
        }

        $body .= '</urlset>';

        return response($body, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }
}
