<?php

namespace App\Services;

class PersianDateService
{
    /**
     * Convert Gregorian date to Persian (Jalali)
     */
    public static function gregorianToPersian($gregorianDate)
    {
        if (is_string($gregorianDate)) {
            $date = \Carbon\Carbon::parse($gregorianDate);
        } else {
            $date = $gregorianDate;
        }

        $jDate = \Morilog\Jalali\Jalalian::fromCarbon($date);

        return [
            'year' => $jDate->getYear(),
            'month' => $jDate->getMonth(),
            'day' => $jDate->getDay(),
            'formatted' => $jDate->format('Y-m-d'),
        ];
    }

    /**
     * Get Persian month name
     */
    public static function getMonthName($monthNumber)
    {
        $months = [
            '',
            'فروردین',
            'اردیبهشت',
            'خرداد',
            'تیر',
            'مرداد',
            'شهریور',
            'مهر',
            'آبان',
            'آذر',
            'دی',
            'بهمن',
            'اسفند',
        ];

        return $months[$monthNumber] ?? 'نامعلوم';
    }

    /**
     * Format Persian date
     */
    public static function formatPersianDate($year, $month, $day)
    {
        return sprintf(
            '%d %s %d',
            $day,
            self::getMonthName($month),
            $year
        );
    }
}
