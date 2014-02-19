<?php

require __DIR__ . '/../src/PrettyDateTime.php';

use PrettyDateTime\PrettyDateTime;

class PrettyDateTimeTestCase extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        // midnight lets us test dates in the future, and with beforeMidnight,
        // those in the past
        $this->midnight = new DateTime('1991-05-18 00:00:00 UTC');
        $this->beforeMidnight = new DateTime('1991-05-18 23:59:59 UTC');
    }

    public function testSingleDateTime()
    {
        $now = new DateTime('now');
        $this->assertEquals('Moments ago', PrettyDateTime::parse($now));
    }

    public function testSameDateTime()
    {
        $now = new DateTime('now');
        $this->assertEquals('Moments ago', PrettyDateTime::parse($now, $now));
    }

    // Testing DateTimes that occurred in the past

    /**
     * @dataProvider pastDateTimesAndStrings
     */
    public function testDateTimesInThePast($timeAgo, $prettyString)
    {
        $dateTime = clone $this->beforeMidnight;
        $dateTime->modify($timeAgo);
        $prettyDateTime = PrettyDateTime::parse($dateTime, $this->beforeMidnight);
        $this->assertEquals($prettyString, $prettyDateTime);
    }

    public function pastDateTimesAndStrings()
    {
        $testData = array(
            array('- 59 second', 'Moments ago'),
            array('- 1 minute', '1 minute ago'),
            array('- 1 hour', '1 hour ago'),
            array(sprintf('- %d second', PrettyDateTime::YEAR), '1 year ago')
        );

        // Test that DateTimes 2..59 minutes prior all say 'x minutes ago'
        for ($i = 2; $i < 60; $i++) {
            array_push($testData, array("- $i minute", "$i minutes ago"));
        }

        // Test that DateTimes 2..23 hours earlier all say 'In x hours'
        for ($i = 2; $i < 24; $i++) {
            array_push($testData, array("- $i hour", "$i hours ago"));
        }

        // Within the past 2..7 days
        for ($i = 2; $i <= 7; $i++) {
            array_push($testData, array("- $i day", "$i days ago"));
        }

        // Within the past 2..5 weeks
        for ($i = 2; $i <= 5; $i++) {
            $days = 7 * $i;
            array_push($testData, array("- $days day", "$i weeks ago"));
        }

        // Within the past 2..11 months
        for ($i = 2; $i <= 11; $i++) {
            $seconds = PrettyDateTime::MONTH * $i + PrettyDateTime::HOUR;
            array_push($testData, array("- $seconds second", "$i months ago"));
        }

        // Within the past 2..20 years
        for ($i = 2; $i <= 20; $i++) {
            $seconds = PrettyDateTime::YEAR * $i;
            array_push($testData, array("- $seconds second", "$i years ago"));
        }

        return $testData;
    }

    public function testYesterday()
    {
        $dateTime = clone $this->midnight;
        $dateTime->modify('- 1 second');
        $prettyDateTime = PrettyDateTime::parse($dateTime, $this->midnight);
        $this->assertEquals('Yesterday', $prettyDateTime);
    }

    // Testing DateTimes that will occur in the future

    /**
     * @dataProvider futureDateTimesAndStrings
     */
    public function testDateTimesInTheFuture($timeFromNow, $prettyString)
    {
        $dateTime = clone $this->midnight;
        $dateTime->modify($timeFromNow);
        $prettyDateTime = PrettyDateTime::parse($dateTime, $this->midnight);
        $this->assertEquals($prettyString, $prettyDateTime);
    }

    public function futureDateTimesAndStrings()
    {
        $testData = array(
            array('+ 59 second', 'Seconds from now'),
            array('+ 1 minute', 'In 1 minute'),
            array('+ 1 hour', 'In 1 hour'),
            array(sprintf('+ %d second', PrettyDateTime::YEAR), 'In 1 year')
        );

        // Test that DateTimes 2..59 minutes later all say 'In x minutes'
        for ($i = 2; $i < 60; $i++) {
            array_push($testData, array("+ $i minute", "In $i minutes"));
        }

        // Test that DateTimes 2..23 hours earlier all say 'In x hours'
        for ($i = 2; $i < 24; $i++) {
            array_push($testData, array("+ $i hour", "In $i hours"));
        }

        // In the next 2..7 days
        for ($i = 2; $i <= 7; $i++) {
            array_push($testData, array("+ $i day", "In $i days"));
        }

        // In the next 2..5 weeks
        for ($i = 2; $i <= 5; $i++) {
            $days = 7 * $i;
            array_push($testData, array("+ $days day", "In $i weeks"));
        }

        // In the next 2..11 months
        for ($i = 2; $i <= 11; $i++) {
            $seconds = PrettyDateTime::MONTH * $i;
            array_push($testData, array("+ $seconds second", "In $i months"));
        }

        // In the next 2..20 years
        for ($i = 2; $i <= 20; $i++) {
            $seconds = PrettyDateTime::YEAR * $i;
            array_push($testData, array("+ $seconds second", "In $i years"));
        }

        return $testData;
    }

    public function testTomorrow()
    {
        $dateTime = clone $this->beforeMidnight;
        $dateTime->modify('+ 1 second');
        $prettyDateTime = PrettyDateTime::parse($dateTime, $this->beforeMidnight);
        $this->assertEquals('Tomorrow', $prettyDateTime);
    }
}
