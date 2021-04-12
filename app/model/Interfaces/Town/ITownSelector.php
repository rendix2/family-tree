<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownSelector.php
 * User: Tomáš Babický
 * Date: 03.04.2021
 * Time: 12:21
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Town\Interfaces;


use Rendix2\FamilyTree\App\Model\Entities\TownEntity;
use Rendix2\FamilyTree\App\Model\Interfaces\ISelector;

interface ITownSelector extends ISelector
{
    /**
     * @param int $countryId
     *
     * @return array
     */
    public function getPairsByCountry($countryId);

    /**
     * @return array
     */
    public function getAllPairs();

    /**
     * @param int $countryId
     *
     * @return TownEntity[]
     */
    public function getAllByCountry($countryId);

    /**
     * @return array
     */
    public function getToMap();
}