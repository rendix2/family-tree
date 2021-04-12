<?php
/**
 *
 * Created by PhpStorm.
 * Filename: CountryManager.php
 * User: Tomáš Babický
 * Date: 02.04.2021
 * Time: 15:04
 */

namespace Rendix2\FamilyTree\App\Model\Managers;

use Rendix2\FamilyTree\App\Filters\CountryFilter;
use Rendix2\FamilyTree\App\Model\CrudManager\CrudManager;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultContainer;
use Rendix2\FamilyTree\App\Model\Managers\Country\CountryTable;

/**
 * Class CountryManager
 *
 * @package Rendix2\FamilyTree\App\Model\Managers
 */
class CountryManager extends CrudManager
{
    /**
     * CountryManager constructor.
     *
     * @param CountryFilter    $countryFilter
     * @param DefaultContainer $defaultContainer
     * @param CountryTable     $table
     */
    public function __construct(
        CountryFilter $countryFilter,
        DefaultContainer $defaultContainer,
        CountryTable $table
    ) {
        parent::__construct($defaultContainer, $table, $countryFilter);
    }
}
