<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use DateTime;



class Ultilities
{

    public static function getDateFromWeek($year, $week)
    {
        $date = Carbon::now();
        $date->setISODate($year, $week);

        $newDate = [
            "startOfWeek" => $date->startOfWeek(),
            "endOfWeek" => $date->endOfWeek()
        ];
        return $newDate;
    }

    public static function getAllMonths($type)
    {
        $months_short = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $months_full = ['January', 'Feburary', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'Octoboer', 'November', 'Decemeber'];
        $months_numeric = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'];
        switch ($type) {
            case 'short':
                return $months_short;
            case 'full':
                return $months_full;
            case 'num':
                return $months_numeric;
        }
    }
}
