# yii2-ip-ratelimiter
Allow guest clients to be rate limited, using their IP as the identifier.

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require ethercretaive/yii2-api-ratelimiter "~1.0.0"
```

or add

```
"ethercreative/yii2-api-ratelimiter": "~1.0.0"
```

to the require section of your `composer.json` file.

## Usage

Modify the bahavior method of the controller you want to rate limit

```
public function behaviors()
{
	$behaviors = parent::behaviors();
	$behaviors['rateLimiter'] = [
		// Use class
		'class' => \ethercreative\ratelimiter\RateLimiter::className(),

		// The maximum number of allowed requests
		'rateLimit' => 100,

		// The time period for the rates to apply to
		'timePeriod' => 600,

		// Separate rate limiting for guests and authenticated users
		// Defaults to true
		// - false: use one set of rates, whether you are authenticated or not
		// - true: use separate ratesfor guests and authenticated users
		'separateRates' => false,

		// Whether to return HTTP headers containing the current rate limiting information
		'enableRateLimitHeaders' => false,
	];
	return $behaviors;
}
```
