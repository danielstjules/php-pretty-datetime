<?php

$base = realpath(dirname(__FILE__) . '/..');
require("$base/src/pretty_datetime.php");

class PrettyDateTimeTestCase extends PHPUnit_Framework_TestCase {

    protected function setUp() {
        // midnight let's us test dates in the future, and with beforeMidnight,
        // those in the past
        $this->midnight = new DateTime('1991-05-18 00:00:00 UTC');
        $this->beforeMidnight = new DateTime('1991-05-18 23:59:59 UTC');
    }

    public function testSingleDateTime() {
        $now = new DateTime('now');
        $this->assertEquals('Moments ago', PrettyDateTime::parse($now));
    }

    public function testSameDateTime() {
        $now = new DateTime('now');
        $this->assertEquals('Moments ago', PrettyDateTime::parse($now, $now));
    }

    // Testing DateTimes that occurred in the past within the same day

    public function testSameDayUnderMinuteAgo() {
        $dateTime = clone $this->beforeMidnight;
        $dateTime->modify('- 59 second');
        $prettyDateTime = PrettyDateTime::parse($dateTime, $this->beforeMidnight);
        $this->assertEquals('Moments ago', $prettyDateTime);
    }

    public function testSameDayMinuteAgo() {
        $dateTime = clone $this->beforeMidnight;
        $dateTime->modify('- 1 minute');
        $prettyDateTime = PrettyDateTime::parse($dateTime, $this->beforeMidnight);
        $this->assertEquals('1 minute ago', $prettyDateTime);
    }

    // Test that DateTimes 2..59 minutes prior all say 'x minutes ago'
    public function testSameDayMinutesAgo() {
        for ($i = 2; $i < 60; $i++) {
            $dateTime = clone $this->beforeMidnight;
            $dateTime->modify("- $i minute");
            $prettyDateTime = PrettyDateTime::parse($dateTime, $this->beforeMidnight);
            $this->assertEquals("$i minutes ago", $prettyDateTime);
        }
    }

    public function testSameDayHourAgo() {
        $dateTime = clone $this->beforeMidnight;
        $dateTime->modify('- 1 hour');
        $prettyDateTime = PrettyDateTime::parse($dateTime, $this->beforeMidnight);
        $this->assertEquals('1 hour ago', $prettyDateTime);
    }

    // Test that DateTimes 2..23 hours earlier all say 'In x hours'
    public function testSameDayHoursAgo() {
        for ($i = 2; $i < 24; $i++) {
            $dateTime = clone $this->beforeMidnight;
            $dateTime->modify("- $i hour");
            $prettyDateTime = PrettyDateTime::parse($dateTime, $this->beforeMidnight);
            $this->assertEquals("$i hours ago", $prettyDateTime);
        }
    }

    // Testing DateTimes that will occur in the same day

    public function testSameDayInUnderAMinute() {
        $dateTime = clone $this->midnight;
        $dateTime->modify('+ 59 second');
        $prettyDateTime = PrettyDateTime::parse($dateTime, $this->midnight);
        $this->assertEquals('Seconds from now', $prettyDateTime);
    }

    public function testSameDayInAMinute() {
        $dateTime = clone $this->midnight;
        $dateTime->modify('+ 1 minute');
        $prettyDateTime = PrettyDateTime::parse($dateTime, $this->midnight);
        $this->assertEquals('In 1 minute', $prettyDateTime);
    }

    // Test that DateTimes 2..59 minutes later all say 'In x minutes'
    public function testSameDayMinutesFromNow() {
        for ($i = 2; $i < 60; $i++) {
            $dateTime = clone $this->midnight;
            $dateTime->modify("+ $i minute");
            $prettyDateTime = PrettyDateTime::parse($dateTime, $this->midnight);
            $this->assertEquals("In $i minutes", $prettyDateTime);
        }
    }

    public function testSameDayHourFromNow() {
        $dateTime = clone $this->midnight;
        $dateTime->modify('+ 1 hour');
        $prettyDateTime = PrettyDateTime::parse($dateTime, $this->midnight);
        $this->assertEquals('In 1 hour', $prettyDateTime);
    }

    // Test that DateTimes 2..23 hours later all say 'In x hours'
    public function testSameDayHoursFromNow() {
        for ($i = 2; $i < 24; $i++) {
            $dateTime = clone $this->midnight;
            $dateTime->modify("+ $i hour");
            $prettyDateTime = PrettyDateTime::parse($dateTime, $this->midnight);
            $this->assertEquals("In $i hours", $prettyDateTime);
        }
    }

    // Test Tomorrow and Today

    public function testTomorrow() {
        $tomorrow = new DateTime('tomorrow');
        $this->assertEquals('Tomorrow', PrettyDateTime::parse($tomorrow));
    }

    public function testYesterday() {
        $yesterday = new DateTime('yesterday');
        $this->assertEquals('Yesterday', PrettyDateTime::parse($yesterday));
    }

    // Within a week

    // Within the past 2..7 days
    public function testPastSevenDays() {
        for ($i = 2; $i <= 7; $i++) {
            $dateTime = new DateTime("- $i day");
            $prettyDateTime = PrettyDateTime::parse($dateTime);
            $this->assertEquals("$i days ago", $prettyDateTime);
        }
    }

    // In the next 2..7 days
    public function testInSevenDays() {
        for ($i = 2; $i <= 7; $i++) {
            $dateTime = new DateTime("+ $i day");
            $prettyDateTime = PrettyDateTime::parse($dateTime);
            $this->assertEquals("In $i days", $prettyDateTime);
        }
    }

    // Within 5 weeks

    // Within the past 2..5 weeks
    public function testPastFiveWeeks() {
        for ($i = 2; $i <= 7; $i++) {
            $days = 7 * $i;
            $dateTime = new DateTime("- $days day");
            $prettyDateTime = PrettyDateTime::parse($dateTime);
            $this->assertEquals("$i weeks ago", $prettyDateTime);
        }
    }

    // In the next 2..5 weeks
    public function testInFiveWeeks() {
        for ($i = 2; $i <= 7; $i++) {
            $days = 7 * $i;
            $dateTime = new DateTime("+ $days day");
            $prettyDateTime = PrettyDateTime::parse($dateTime);
            $this->assertEquals("In $i weeks", $prettyDateTime);
        }
    }
}

?>
