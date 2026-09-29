<?php

namespace App\Support;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

/** แปลงวันที่งวดหวยเป็นรูปแบบไทย และ slug สำหรับ URL เช่น 16-กันยายน-2569 */
class ThaiDate
{
    public const MONTHS = [
        1 => 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
        'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม',
    ];

    public const SHORT_MONTHS = [
        1 => 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.',
        'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.',
    ];

    public static function long(CarbonInterface $date): string
    {
        return $date->day.' '.self::MONTHS[$date->month].' '.($date->year + 543);
    }

    public static function short(CarbonInterface $date): string
    {
        return $date->day.' '.self::SHORT_MONTHS[$date->month].' '.($date->year + 543);
    }

    public static function slug(CarbonInterface $date): string
    {
        return $date->day.'-'.self::MONTHS[$date->month].'-'.($date->year + 543);
    }

    public static function fromSlug(string $slug): ?Carbon
    {
        if (! preg_match('/^(\d{1,2})-(\p{Thai}+)-(\d{4})$/u', $slug, $m)) {
            return null;
        }

        $month = array_search($m[2], self::MONTHS, true);
        $year = (int) $m[3] - 543;

        if ($month === false || ! checkdate($month, (int) $m[1], $year)) {
            return null;
        }

        return Carbon::create($year, $month, (int) $m[1])->startOfDay();
    }
}
