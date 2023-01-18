<?php

namespace andreyv\ratelimiter;

use Yii;
use yii\filters\RateLimiter;

class IpRateLimiter extends RateLimiter
{
    /**
     * @var boolean whether to separate rate limiting between non and authenticated users
     */
    public $separateRates = false;

    /**
     * @var integer the maximum number of allowed requests
     */
    public $rateLimit = 5;

    /**
     * @var integer the time period for the rates to apply to
     */
    public $timePeriod = 1;

    /**
     * @var array list of actions on which to apply ratelimiter, if empty - applies to all actions
     */
    public $actions = [];

    /**
     * @var bool allows to skip rate limiting for test environment
     */
    public $testMode = false;

    /**
     * @var bool defines whether proxy enabled
     */
    public $proxyEnabled = false;

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if ($this->testMode) {
            return true;
        }

        if (is_array($this->actions) && (empty($this->actions) || in_array($action->id, $this->actions))) {
            if ($this->separateRates && !$this->user) {
                $this->user = Yii::$app->getUser() ? Yii::$app->getUser()->getIdentity(false) : null;
            }

            if (!$this->user) {
                /** @var IpRateLimitInterface $identityClass */
                $identityClass = Yii::$app->getUser()->identityClass;
                if (!in_array(UserIdentity::class, class_implements($identityClass))) {
                    $identityClass = UserIdentity::class;
                }

                $this->user = $identityClass::create(
                    $this->request->getUserIP(),
                    $this->rateLimit,
                    $this->timePeriod
                );
            }

            return parent::beforeAction($action);
        }
        return true;
    }
}
