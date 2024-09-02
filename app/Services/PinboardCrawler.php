<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class PinboardCrawler
{
    /**
     * The base URL for pinboard.
     */
    protected const BASE_URL = 'https://pinboard.in';

    /**
     * Crawl the pinboard search page.
     */
    public function crawl(): Crawler
    {
        $response = Http::get('https://pinboard.in/u:alasdairw?per_page=120');

        if ($response->ok()) {
            return new Crawler($response->body());
        }
    }
}
