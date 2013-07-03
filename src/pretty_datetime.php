<?php

class PrettyDateTime {

    const MINUTE = 60;
    const HOUR   = 3600;
    const DAY    = 86400;
    const WEEK   = 604800;
    const MONTH  = 2629742;
    const YEAR   = 31536000;

    private static function prettyFormat($difference, $unit) {
        // $prepend is added to the start of the string if the supplied 
        // difference is greater than 0, and $append if less than
        $prepend = ($difference < 0) ? 'In ' : '';
        $append = ($difference > 0) ? ' ago' : '';

        $difference = floor(abs($difference));

        // If difference is plural, add an 's' to $unit
        if ($difference > 1)
            $unit = $unit . 's';

        return sprintf('%s%d %s%s', $prepend, $difference, $unit, $append);
    }

    public static function parse(DateTime $dateTime, DateTime $originalDateTime = null) {
        // If not provided, set $originalDateTime to the current DateTime
        if (!$originalDateTime)
            $originalDateTime = new DateTime('now');

        // Get the difference between the current date and the supplied $dateTime
        $difference = $originalDateTime->format('U') - $dateTime->format('U');
        $absDiff = abs($difference);

        // Throw exception if the difference is NaN
        if (is_nan($difference))
            throw new Exception('The difference between the DateTimes is NaN.');

        // Today
        if ($originalDateTime->format('Y/m/d') == $dateTime->format('Y/m/d')) {
            if (0 <= $difference && $absDiff < self::MINUTE) {
                return 'Moments ago';
            } elseif ($difference < 0 && $absDiff < self::MINUTE) {
                return 'Seconds from now';
            } elseif ($absDiff < self::HOUR) {
                return self::prettyFormat($difference / self::MINUTE, 'minute');
            } else {
                return self::prettyFormat($difference / self::HOUR, 'hour');
            }
        }

        // Yesterday
        if (date('Y/m/d', strtotime('yesterday')) == $dateTime->format('Y/m/d'))
            return 'Yesterday';

        // Tomorrow
        if (date('Y/m/d', strtotime('tomorrow')) == $dateTime->format('Y/m/d'))
            return 'Tomorrow';
    }
}

?>
