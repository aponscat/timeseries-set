## TimeSeriesSet Package v1.0.0

This package allows you to add different "tags" in different times, the Set "counts" the number of "tags" that appears in a given period of time.

The default time period is "one minute" ('YmdHi'). You can change this behaviour with the second parameter of the object constructor. For exemple, create a time period counter of "one hour" using the value 'YmdH'.

Usage:
```
use Apons\TimeSeriesSet\TimeSeriesSet;
use Apons\TimeSeriesSet\Adapters\Memcached\MemcachedSet;
use Apons\TimeSeriesSet\Adapters\Memcached\MemcachedSetStorage;
use Apons\TimeSeriesSet\Interfaces\SetStorageInterface;
...

// Create a new memcached object
$m = new \Memcached();
$m->addServer('localhost', 11211);
$m->setOption(\Memcached::OPT_COMPRESSION, false);

// Create a MemcachedSetStorage object injecting the memcached connection
// The second parameter indicates the number of seconds that memcached 
// will keep the items stored (default 3.600 seconds (1 hour))
$s=new MemcachedSetStorage($m, 3600);

// Create a new TimeSeriesSet injecting the MemcachedSet newly created
// note than the second parameter sets the time frame used
// For example:
// 'YmdHi' --> minute intervals
// 'YmdH'  --> hour interval
$timeSeriesSet=new TimeSeriesSet(new MemcachedSet($s), 'YmdHi');

// Add the tag you want to count, by default counts this tag in the current time()
// use the second optional parameter to pass a timestamp (used for testing purposes usually)
$timeSeriesSet->add('tag1999');

or 

$timeSeriesSet->add('tag1999', (new DateTime("2022-02-02 10:01:02"))->getTimestamp());
...

// Finally count the tags in a given time period
$set=$timeSeriesSet->getAllTagsInTime((new DateTime("2022-02-02 10:00"))->getTimestamp());

// Returns an array of tags with is count
// For example, if we add tag1999 in the given time ("2022-02-02 10:00")
// The result will be ['tag1999'=>1]
```

Of course the add and count (getAllTagsInTime) can (and usually should) be in different processes and executions.

A typical use case is a Rate Limiter. 

In a rate limiter every request to a given url can add to the TimeSeriesSet by tag
Afterwards a cron job can count the requests to all the given tags in a timeframe and decide if the limit has been reached.

Example requests to be counted:
/sample-url/27773 (2022-02-02 10:00:03)
/sample-url/27773 (2022-02-02 10:00:08)
/sample-url/27773 (2022-02-02 10:00:11)
/sample-url/27773 (2022-02-02 10:00:12)
/sample-url/27773 (2022-02-02 10:00:25)
/sample-url/27773 (2022-02-02 10:00:59)

/sample-url/19999 (2022-02-02 10:00:05)
/sample-url/19999 (2022-02-02 10:00:12)
/sample-url/19999 (2022-02-02 10:00:33)
/sample-url/19999 (2022-02-02 10:00:55)

Then in the cronjob that counts all requests by tag in the timeframe (2022-02-02 10:00 to 2022-02-02 10:59) the result will be:

```
[
    '27773'=>6,
    '19999'=>4
]
```

# Testing
Test the package with

```
./vendor/bin/phpunit tests
```
