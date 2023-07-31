<?php

abstract class Util {

    public static function timeFormat($seconds, $showEmptyMinutes = true, $showEmptyHours = false) {

        $format = array();

        $hours = floor($seconds / 3600);
        $mins = floor(($seconds - ($hours*3600)) / 60);
        $secs = floor($seconds % 60);

        if ($seconds >= 3600 || ($showEmptyMinutes && $showEmptyHours)) {
            $format[] = sprintf('%02d', $hours);
        }

        if ($seconds >= 60 || $showEmptyMinutes) {
            $format[] = sprintf('%02d', $mins);
        }

        $format[] = sprintf('%02d', $secs);

        $format = implode(':', $format);

        return $format;
    }

    public static function tdateLocal($format , $date = null) {
        return tdate($format, $date, 'UTC', Configure::read('datetime.timezone', 'UTC'));
    }
}
