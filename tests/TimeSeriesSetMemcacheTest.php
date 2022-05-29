<?php

declare(strict_types=1);
use PHPUnit\Framework\TestCase;
use Apons\TimeSeriesSet\TimeSeriesSet;
use Apons\TimeSeriesSet\MemcachedSet;
use Apons\TimeSeriesSet\SetStorageInterface;
use Apons\TimeSeriesSet\MemcachedSetStorage;

final class TimeSeriesSetMemcacheTest extends TestCase
{
    public SetStorageInterface $s;

    public function setUp (): void
    {
        $m = new \Memcached();
        $m->addServer('localhost', 11211);
        $m->setOption(\Memcached::OPT_COMPRESSION, false);
        $this->s=new MemcachedSetStorage($m);
    }

    public function testSameTagInCurrentMinute()
    {
        $time = time();
        $tag='testtag';
        $timeSeriesSet=new TimeSeriesSet(new MemcachedSet($this->s), 'YmdHi');
        $timeSeriesSet->clear();
        $timeSeriesSet->add($tag, $time);
        $set=$timeSeriesSet->getAllTagsInTime($time);
        $this->assertTrue($set[$tag]==1);
    }

    public function testSameTagOneTimesInSameMinute()
    {
        $time1 = (new DateTime("2022-02-02 10:01:02"))->getTimestamp();
        $tag1='tag1';
        $timeSeriesSet=new TimeSeriesSet(new MemcachedSet($this->s), 'YmdHi');
        $timeSeriesSet->clear();
        $timeSeriesSet->add($tag1, $time1);
        $set=$timeSeriesSet->getAllTagsInTime((new DateTime("2022-02-02 10:01"))->getTimestamp());
        $this->assertTrue($set[$tag1]==1);
    }

    public function testSameTag4TimesSameTagInSameMinute()
    {
        $time1 = (new DateTime("2022-02-02 10:01:02"))->getTimestamp();
        $time2 = (new DateTime("2022-02-02 10:01:05"))->getTimestamp();
        $time3 = (new DateTime("2022-02-02 10:01:15"))->getTimestamp();
        $time4 = (new DateTime("2022-02-02 10:01:25"))->getTimestamp();

        $tag1='tag1';
        $timeSeriesSet=new TimeSeriesSet(new MemcachedSet($this->s), 'YmdHi');
        $timeSeriesSet->clear();
        $timeSeriesSet->add($tag1, $time1);
        $timeSeriesSet->add($tag1, $time2);
        $timeSeriesSet->add($tag1, $time3);
        $timeSeriesSet->add($tag1, $time4);
        $set=$timeSeriesSet->getAllTagsInTime((new DateTime("2022-02-02 10:01"))->getTimestamp());
        $this->assertTrue($set[$tag1]==4);
    }

    public function testSameTag4TimesSameTagInSameMinuteSearchForOtherMinute()
    {
        $time1 = (new DateTime("2022-02-02 10:01:02"))->getTimestamp();
        $time2 = (new DateTime("2022-02-02 10:01:05"))->getTimestamp();
        $time3 = (new DateTime("2022-02-02 10:01:15"))->getTimestamp();
        $time4 = (new DateTime("2022-02-02 10:01:25"))->getTimestamp();

        $tag1='tag1';
        $timeSeriesSet=new TimeSeriesSet(new MemcachedSet($this->s), 'YmdHi');
        $timeSeriesSet->clear();
        $timeSeriesSet->add($tag1, $time1);
        $timeSeriesSet->add($tag1, $time2);
        $timeSeriesSet->add($tag1, $time3);
        $timeSeriesSet->add($tag1, $time4);
        $set=$timeSeriesSet->getAllTagsInTime((new DateTime("2022-02-02 15:00"))->getTimestamp());
        $this->assertTrue($set==null);
    }


    public function testSameTag2TimesIn1MinuteAnd2TimesInOther()
    {
        $time1 = (new DateTime("2022-02-02 10:01:02"))->getTimestamp();
        $time2 = (new DateTime("2022-02-02 10:01:05"))->getTimestamp();
        $time3 = (new DateTime("2022-02-02 10:05:03"))->getTimestamp();
        $time4 = (new DateTime("2022-02-02 10:05:25"))->getTimestamp();

        $tag1='tag1';
        $timeSeriesSet=new TimeSeriesSet(new MemcachedSet($this->s));
        $timeSeriesSet->clear();
        $timeSeriesSet->add($tag1, $time1);
        $timeSeriesSet->add($tag1, $time2);
        $timeSeriesSet->add($tag1, $time3);
        $timeSeriesSet->add($tag1, $time4);
        $set=$timeSeriesSet->getAllTagsInTime((new DateTime("2022-02-02 10:01"))->getTimestamp());
        $this->assertTrue($set[$tag1]==2);
        $set=$timeSeriesSet->getAllTagsInTime((new DateTime("2022-02-02 10:05"))->getTimestamp());
        $this->assertTrue($set[$tag1]==2);
        $set=$timeSeriesSet->getAllTagsInTime((new DateTime("2022-02-02 15:00"))->getTimestamp());
        $this->assertTrue($set==null);
    }


    public function testTwoTagsWith3And1InOneMinute()
    {
        $time1 = (new DateTime("2022-02-02 10:01:02"))->getTimestamp();
        $time2 = (new DateTime("2022-02-02 10:01:05"))->getTimestamp();
        $time3 = (new DateTime("2022-02-02 10:01:03"))->getTimestamp();
        $time4 = (new DateTime("2022-02-02 10:01:25"))->getTimestamp();

        $tag1='tag1';
        $tag2='tag2';
        $timeSeriesSet=new TimeSeriesSet(new MemcachedSet($this->s));
        $timeSeriesSet->clear();
        $timeSeriesSet->add($tag1, $time1);
        $timeSeriesSet->add($tag1, $time2);
        $timeSeriesSet->add($tag1, $time3);
        $timeSeriesSet->add($tag2, $time4);
        $set=$timeSeriesSet->getAllTagsInTime((new DateTime("2022-02-02 10:01"))->getTimestamp());
        $this->assertTrue($set[$tag1]==3);
        $this->assertTrue($set[$tag2]==1);
        $set=$timeSeriesSet->getAllTagsInTime((new DateTime("2022-02-02 15:00"))->getTimestamp());
        $this->assertTrue($set==null);
    }

    public function test1TagxSecond2TagsxSecondAnd3TagsxSecondDuring2Minutes()
    {

        $start = (new DateTime("2022-02-02 10:01:00"))->getTimestamp();
        $timeSeriesSet=new TimeSeriesSet(new MemcachedSet($this->s));
        $timeSeriesSet->clear();

        foreach (range(0,120) as $second)
        {
            $time=$start+$second;
            $timeSeriesSet->add(1, $time);

            $timeSeriesSet->add(2, $time);
            $timeSeriesSet->add(2, $time);

            $timeSeriesSet->add(3, $time);
            $timeSeriesSet->add(3, $time);
            $timeSeriesSet->add(3, $time);
        }
        
        $set=$timeSeriesSet->getAllTagsInTime((new DateTime("2022-02-02 10:01"))->getTimestamp());
        $this->assertTrue($set[1]==60);
        $this->assertTrue($set[2]==120);
        $this->assertTrue($set[3]==180);
        $set=$timeSeriesSet->getAllTagsInTime((new DateTime("2022-02-02 10:02"))->getTimestamp());
        $this->assertTrue($set[1]==60);
        $this->assertTrue($set[2]==120);
        $this->assertTrue($set[3]==180);
    }


    public function testNtagsxSecondDuring2Minutes()
    {

        $start = (new DateTime("2022-02-02 10:01:00"))->getTimestamp();
        $timeSeriesSet=new TimeSeriesSet(new MemcachedSet($this->s));
        $timeSeriesSet->clear();

        foreach (range(0,120) as $second)
        {
            $time=$start+$second;
            foreach (range(1,3) as $tag)
            {
                $i=0;
                while ($i<($tag*$second))
                {
                    $timeSeriesSet->add($tag, $time);
                    $i++;
                }
            }
        }
        
        $set=$timeSeriesSet->getAllTagsInTime((new DateTime("2022-02-02 10:01"))->getTimestamp());
        $this->assertTrue($set[1]==1770);
        $this->assertTrue($set[2]==1770*2);
        $this->assertTrue($set[3]==1770*3);
        $set=$timeSeriesSet->getAllTagsInTime((new DateTime("2022-02-02 10:02"))->getTimestamp());
        $this->assertTrue($set[1]==5370);
        $this->assertTrue($set[2]==5370*2);
        $this->assertTrue($set[3]==5370*3);
    }

}