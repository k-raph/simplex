<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 02/02/2019
 * Time: 00:20
 */

namespace App\BlogModule\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigTextExtension extends AbstractExtension
{
    /**
     * @return array|TwigFilter[]
     */
    public function getFilters()
    {
        return [
            new TwigFilter('excerpt', function (string $input, int $length = 100, string $end = '...') {
                $result = explode(" ", $input);
                if (count($result) > $length) {
                    $result = implode(" ", array_slice($result, 0, $length));
                }

                return "$result$end";
            })
        ];
    }
}
