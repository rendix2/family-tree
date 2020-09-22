<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SinceToHelper.php
 * User: Tomáš Babický
 * Date: 22.09.2020
 * Time: 20:20
 */

namespace Rendix2\FamilyTree\App\Filters;

use DateTime;

/**
 * Class SinceToHelper
 *
 * @package Rendix2\FamilyTree\App\Filters
 */
class SinceToHelper
{
    /**
     * @param DateTime|null $since
     * @param DateTime|null $to
     *
     * @return string
     */
    public static function sinceTo(DateTime $since = null, DateTime $to = null)
    {
        if ($since && $to) {
            return '(' . $since->format('y.m.dd ') . '-' . $to->format('yy.mm.dd ') . ')';
        } elseif ($since && !$to) {
            return '(' . $since->format('d.m.Y') . ' - NA)';
        } elseif (!$since && $to) {
            return '(NA - ' . $to->format('d.m.Y') . ')';
        } else {
            return '';
        }
    }
}
