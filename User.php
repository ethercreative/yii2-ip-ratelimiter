<?php

namespace ethercreative\ratelimiter;

use Yii;

class User extends \yii\base\Model
{
	/**
	 * @var the IP of the user
	 */
	private $ip;

	/**
	 * @var the maximum number of allowed requests
	 */
	private $rateLimit;

	/**
	 * @var the time period for the rates to apply to
	 */
	private $timePeriod;

	/**
	 * Returns a surrogate user with the IP address assigned.
	 * @param string $ip the IP of the client.
	 * @return User the user component.
	 */
	public static function findByIp($ip, $rateLimit, $timePeriod)
	{
		$user = new User;
		$user->ip = $ip;
		$user->rateLimit = $rateLimit;
		$user->timePeriod = $timePeriod;

		return $user;
	}

	/**
	 * Returns the maximum number of allowed requests and the window size.
	 * @param \yii\web\Request $request the current request
	 * @param \yii\base\Action $action the action to be executed
	 * @return array an array of two elements. The first element is the maximum number of allowed requests,
	 * and the second element is the size of the window in seconds.
	 */
	public function getRateLimit($request, $action)
	{
		return [$this->rateLimit, $this->timePeriod];
	}

	/**
	 * Loads the number of allowed requests and the corresponding timestamp from a persistent storage.
	 * @param \yii\web\Request $request the current request
	 * @param \yii\base\Action $action the action to be executed
	 * @return array an array of two elements. The first element is the number of allowed requests,
	 * and the second element is the corresponding UNIX timestamp.
	 */
	public function loadAllowance($request, $action)
	{
		$cache = Yii::$app->cache;
		return [
			$cache->get('user.ratelimit.ip.allowance.' . $this->ip),
			$cache->get('user.ratelimit.ip.allowance_updated_at.' . $this->ip),
		];
	}

	/**
	 * Saves the number of allowed requests and the corresponding timestamp to a persistent storage.
	 * @param \yii\web\Request $request the current request
	 * @param \yii\base\Action $action the action to be executed
	 * @param integer $allowance the number of allowed requests remaining.
	 * @param integer $timestamp the current timestamp.
	 */
	public function saveAllowance($request, $action, $allowance, $timestamp)
	{
		$cache = Yii::$app->cache;
		$cache->set('user.ratelimit.ip.allowance.' . $this->ip, $allowance);
		$cache->set('user.ratelimit.ip.allowance_updated_at.' . $this->ip, $timestamp);
	}
}
