<?php

namespace ethercreative\ratelimiter;

use Yii;

/**
 * Class RateLimiter
 *
 * @package ethercreative\ratelimiter
 */
class RateLimiter extends \yii\filters\RateLimiter
{
    /**
     * @var boolean whether to separate rate limiting between non and authenticated users
     */
    public $separateRates = true;

    /**
     * @var integer the maximum number of allowed requests
     */
    public $rateLimit;

    /**
     * @var integer the time period for the rates to apply to
     */
    public $timePeriod;

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $user = $this->user;

        if ($this->separateRates) {
            $user = $user ?: (Yii::$app->getUser() ? Yii::$app->getUser()->getIdentity(false) : null);
        }

        /** @var IpRateLimitInterface $identityClass */
        $identityClass = Yii::$app->getUser()->identityClass;

        $user = $user ?: $identityClass::findByIp(Yii::$app->getRequest()->getUserIP(), $this->rateLimit,
            $this->timePeriod);

        if ($user instanceof IpRateLimitInterface) {
            Yii::trace('Check rate limit', __METHOD__);

            $this->checkRateLimit(
                $user,
                $this->request ?: Yii::$app->getRequest(),
                $this->response ?: Yii::$app->getResponse(),
                $action
            );

            return true;
        }

        return parent::beforeAction($action);
    }
}
