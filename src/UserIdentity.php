<?php

namespace andreyv\ratelimiter;

use Yii;

class UserIdentity implements IpRateLimitInterface
{
    /**
     * @var string IP of the user
     */
    private $ip;

    /**
     * @var integer maximum number of allowed requests
     */
    private $rateLimit;

    /**
     * @var integer time period for the rates to apply to
     */
    private $timePeriod;

    /**
     * @inheritdoc
     */
    public static function findByIp($ip, $rateLimit, $timePeriod)
    {
        $user = new static();

        $user->ip = $ip;
        $user->rateLimit = $rateLimit;
        $user->timePeriod = $timePeriod;

        return $user;
    }

    /**
     * @inheritdoc
     */
    public function getRateLimit($request, $action)
    {
        return [$this->rateLimit, $this->timePeriod];
    }

    /**
     * @inheritdoc
     */
    public function loadAllowance($request, $action)
    {
        $cache = Yii::$app->getCache();

        return [
            $cache->get('user.ratelimit.ip.allowance.' . $this->ip),
            $cache->get('user.ratelimit.ip.allowance_updated_at.' . $this->ip),
        ];
    }

    /**
     * @inheritdoc
     */
    public function saveAllowance($request, $action, $allowance, $timestamp)
    {
        $cache = Yii::$app->getCache();

        $cache->set('user.ratelimit.ip.allowance.' . $this->ip, $allowance);
        $cache->set('user.ratelimit.ip.allowance_updated_at.' . $this->ip, $timestamp);
    }
}
