<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 13/07/2019
 * Time: 18:56
 */

namespace Simplex\Helper;


class Str
{

    /**
     * @param string $string
     * @return string
     */
    static public function slugify(string $string): string
    {
        $string = preg_replace('~\W+~', '-', $string);
        $string = trim($string, '-');

        return strtolower($string);
    }

    /**
     * @param int $length
     * @return string
     * @throws \Exception
     */
    static public function random(int $length = 20): string
    {
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;
            $bytes = random_bytes($size);
            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }
}