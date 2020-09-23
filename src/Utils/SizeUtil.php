<?php

namespace Sim\File\Utils;

class SizeUtil
{
    /**
     * @see https://stackoverflow.com/questions/11807115/php-convert-kb-mb-gb-tb-etc-to-bytes
     * @param string $from
     * @param int|null $prefer
     * @return int|null
     */
    public static function convertToBytes(string $from, ?int $prefer = 0): ?int
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $number = substr($from, 0, -2);
        $suffix = strtoupper(substr($from, -2));

        //B or no suffix
        if (is_numeric(substr($suffix, 0, 1))) {
            return preg_replace('/[^\d]/', '', $from);
        }

        $exponent = array_flip($units)[$suffix] ?? null;
        if (is_null($exponent)) {
            return $prefer;
        }

        return $number * (1024 ** $exponent);
    }

    /**
     * @see https://stackoverflow.com/questions/2510434/format-bytes-to-kilobytes-megabytes-gigabytes?noredirect=1&lq=1
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    public static function formatBytes(int $bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        // Uncomment one of the following alternatives
        // $bytes /= pow(1024, $pow);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . $units[$pow];
    }
}