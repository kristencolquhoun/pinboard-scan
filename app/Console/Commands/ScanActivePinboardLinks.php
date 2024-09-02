<?php

namespace App\Console\Commands;

use App\Models\Bookmark;
use App\Models\Tag;
use App\Services\PinboardCrawler;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\DomCrawler\Crawler;

class ScanActivePinboardLinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:scan-active-pinboard-links';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawl pinboard and save the bookmarks';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $crawler = new PinboardCrawler;

        $crawler->crawl()->filter('.bookmark')
            ->each(function (Crawler $node) use (&$bookmarks) {
                $tags = collect($node->filter('.tag')->extract(['_text']));

                if ($tags->contains(function (string $value) {
                    return in_array($value, ['laravel', 'vue', 'vue.js', 'php', 'api']);
                })) {
                    $this->updateOrCreateBookmark($node->attr('id'), [
                        'title' => $node->filter('.bookmark_title')->innerText(),
                        'description' => $node->filter('.description')->innerText(),
                        'url' => $node->filter('.bookmark_title')->attr('href'),
                        'tags' => $tags,
                        'is_valid' => true,
                    ]);
                }
            });
    }

    /**
     * Update or create bookmark from pinboard.
     */
    protected function updateOrCreateBookmark(int $id, array $attributes): void
    {
        $bookmark = Bookmark::updateOrCreate(
            [
                'pinboard_id' => $id,
            ],
            array_merge(
                Arr::only($attributes, ['title', 'description', 'url']),
                ['is_active' => $this->validateUrl($attributes['url'])]
            )
        );

        $tags = $attributes['tags']->map(function ($tag) {
            return Tag::firstOrCreate(['name' => strtolower($tag)]);
        });

        $bookmark->tags()->sync($tags->pluck('id'));
    }

    /**
     * Validate a given URL using laravel validation.
     */
    protected function validateUrl(string $url): bool
    {
        $validator = Validator::make(['url' => $url], ['url' => 'active_url']);

        return $validator->passes();
    }
}
