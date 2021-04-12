<?php
/**
 *
 * Created by PhpStorm.
 * Filename: CountryFilter.php
 * User: Tomáš Babický
 * Date: 04.10.2020
 * Time: 23:36
 */

namespace Rendix2\FamilyTree\App\Filters;

use Rendix2\FamilyTree\App\Model\Entities\CountryEntity;

/**
 * Class CountryFilter
 *
 * @package Rendix2\FamilyTree\App\Forms
 */
class CountryFilter implements IFilter
{
    /**
     * @param CountryEntity $country
     *
     * @return string
     */
    public function __invoke(CountryEntity $country)
    {
        return $country->name;
    }
}
