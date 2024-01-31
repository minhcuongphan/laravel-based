<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScraperRequest;
use App\Jobs\ScrapingJob;
use Illuminate\Support\Facades\Redis;

class ScraperController extends Controller
{
    public function index()
    {
        $results = [];
        $scrapedJobs = Redis::keys(config('master.prefix_redis_key') . '*');
        foreach ($scrapedJobs as $scrapedJob) {
            $results[$scrapedJob] = json_decode(Redis::get($scrapedJob), true);
        }

        return $this->sendResponse($results, '');
    }

    public function show($jobKey)
    {
        if (!Redis::exists($jobKey)) {
            return $this->sendError('URL does not exist');
        }

        return $this->sendResponse(json_decode(Redis::get($jobKey), true), '');
    }

    public function store(ScraperRequest $request)
    {
        $inputs = $request->validated();
        foreach (array_chunk($inputs['urls'], 10) as $chunkedUrls) {
            ScrapingJob::dispatch($chunkedUrls, $inputs['selectors'])->onQueue('scraping');
        }

        return $this->sendResponse([], 'Successfully created new jobs');
    }

    public function destroy($jobKey)
    {
        if (!Redis::exists($jobKey)) {
            return $this->sendError('URL does not exist');
        }

        Redis::del($jobKey);

        return $this->sendResponse([], 'Successfully deleted a job');
    }
}
