<?php

namespace andreyv\ratelimiter;

use yii\filters\RateLimitInterface;

interface IpRateLimitInterface extends RateLimitInterface
{
    /**
     * Returns a surrogate user with the IP address assigned.
     *
     * @param string $ip
     * @param int $rateLimit
     * @param int $timePeriod
     *
     * @return static
     */
    public static function create($ip, $rateLimit, $timePeriod);
}