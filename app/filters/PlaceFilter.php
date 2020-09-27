<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PlaceFilter.php
 * User: Tomáš Babický
 * Date: 24.09.2020
 * Time: 1:44
 */

namespace Rendix2\FamilyTree\App\Filters;

use Dibi\Row;

/**
 * Class PlaceFilter
 *
 * @package Rendix2\FamilyTree\App\Filters
 */
class PlaceFilter
{

    /**
     * @param Row $place
     */
    public function __invoke(Row $place)
    {
        return $place->name;
    }
}
