<?php

/*
 * (c) Jakub Ujwary <jakub.ujwary@gmail.com>
 */

namespace App\Utils;

use DateInterval;

class Pesel
{
    public static function getDate(string $pesel): ?\DateTime
    {
        list($year, $month, $day) = sscanf($pesel, '%02s%02s%02s');
        switch (substr($month, 0, 1)) {
            case 2:
            case 3:
                $month -= 20;
                $year += 2000;
                break;
            case 4:
            case 5:
                $month -= 40;
                $year += 2100;
            case 6:
            case 7:
                $month -= 60;
                $year += 2200;
                break;
            case 8:
            case 9:
                $month -= 80;
                $year += 1800;
                break;
            default:
                $year += 1900;
                break;
        }

        return checkdate($month, $day, $year)
            ? new \DateTime("$year/$month/$day")
            : null;
    }

    public static function getAge($pesel): int
    {
        $diff = self::getDate($pesel)->diff(new \DateTime('now'));
        return $diff->y;
    }

    public static function getAdolescentInterval($pesel): DateInterval
    {
        $birthDate = self::getDate($pesel);
        $adolescentDate = $birthDate->modify('+18 years');
        $diff = $adolescentDate->diff(new \DateTime('now'));
        return $diff;
    }

    public static function validateCheckSum(string $pesel): bool
    {
        $weights = [1, 3, 7, 9, 1, 3, 7, 9, 1, 3, 1];

        $digits = str_split($pesel);

        $checksum = array_reduce(array_keys($digits), function ($carry, $index) use ($weights, $digits) {
            return $carry + $weights[$index] * $digits[$index];
        });

        if ($checksum % 10 !== 0) {
            // incorrect checksum
            return false;
        }
        return true;
    }
}

