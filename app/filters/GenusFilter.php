<?php
/**
 *
 * Created by PhpStorm.
 * Filename: GenusFilter.php
 * User: Tomáš Babický
 * Date: 24.09.2020
 * Time: 2:03
 */

namespace Rendix2\FamilyTree\App\Filters;

use Dibi\Row;

/**
 * Class GenusFilter
 *
 * @package Rendix2\FamilyTree\App\Filters
 */
class GenusFilter
{
    /**
     * @param Row $genus
     */
    public function __invoke(Row $genus)
    {
        return $genus->surname;
    }
}
