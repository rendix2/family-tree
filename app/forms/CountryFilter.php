<?php
/**
 *
 * Created by PhpStorm.
 * Filename: CountryFilter.php
 * User: Tomáš Babický
 * Date: 04.10.2020
 * Time: 23:36
 */

namespace Rendix2\FamilyTree\App\Forms;

use Dibi\Row;

/**
 * Class CountryFilter
 *
 * @package Rendix2\FamilyTree\App\Forms
 */
class CountryFilter
{
    /**
     * @param Row $country
     */
    public function __invoke(Row $country)
    {
        return $country->name;
    }
}
