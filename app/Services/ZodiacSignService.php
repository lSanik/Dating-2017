<?php
/**
 * Created by PhpStorm.
 * User: Oleh Hrebeniuk
 * Date: 16.08.2016
 * Time: 22:01
 */

namespace App\Services;


final class ZodiacSignService
{
    /**
     * @var array
     */
    private static $signs = [
        356 => 'Capricorn',
        326 => 'Sagittarius',
        296 => 'Scorpio',
        266 => 'Libra',
        235 => 'Virgo',
        203 => 'Leo',
        172 => 'Cancer',
        140 => 'Gemini',
        111 => 'Taurus',
        78  => 'Aries',
        51  => 'Pisces',
        20  => 'Aquarius',
        0   => 'Capricorn',
    ];


    public static function getSignByBirthday($date = '')
    {
        $sign = '';

        if (!$date) $date = new \DateTime();

        $dayOfYear = date('z', strtotime($date));
        $isLeapYear = date('L', strtotime($date));

        if ($isLeapYear && ($dayOfYear > 59)) {
            $dayOfYear -= 1;
        }

        foreach (self::$signs as $day => $sign) {
            if ($dayOfYear > $day) break;
        }

        return $sign;
    }
    public static function getAll(){
        $list=self::$signs;
        unset($list[0]);
        return $list;
    }
}