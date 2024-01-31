<?php

namespace App\Jobs;

use App\Notifications\JobFailedNotification;
use Goutte\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;
use Throwable;
class ScrapingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $urls;
    protected $selectors;

    public function __construct(array $urls, array $selectors)
    {
        $this->urls = $urls;
        $this->selectors = $selectors;
    }

    public function handle()
    {
        try {
            $client = new Client();
            foreach ($this->urls as $url) {
                $data = [];
                $crawler = $client->request('GET', $url);

                foreach ($this->selectors as $selector) {
                    $data[] = $crawler->filter($selector)->each(function ($node) use ($selector) {
                        return [
                            'selector' => $selector,
                            'content' => $node->text(),
                        ];
                    });
                }

                // Store data in Redis
                Redis::set(generateRedisKey($url), json_encode($data));
            }
        } catch (\Exception $e) {
            $this->failed($e);
        }
    }

    public function failed(Throwable $exception)
    {
        // Send email notification on job failure
        foreach (config('master.admin_emails') as $admin) {
            $admin->notify(new JobFailedNotification(static::class, $exception->getMessage()));
        }
    }
}
