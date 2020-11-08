<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownFilter.php
 * User: Tomáš Babický
 * Date: 24.09.2020
 * Time: 1:44
 */

namespace Rendix2\FamilyTree\App\Filters;

use Dibi\Row;

/**
 * Class TownFilter
 *
 * @package Rendix2\FamilyTree\App\Filters
 */
class TownFilter
{
    /**
     * @param Row $town
     * @return string
     */
    public function __invoke(Row $town)
    {
        return $town->name . ' ' . $town->zipCode;
    }
}
