<?php

/**
 * Generating an unique identifier
 *
 * @param string $url
 * @return string
 */
function generateRedisKey($url)
{
    return config('master.prefix_redis_key') . md5($url);
}