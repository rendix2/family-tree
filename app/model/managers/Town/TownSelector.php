<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownSelector.php
 * User: Tomáš Babický
 * Date: 03.04.2021
 * Time: 12:28
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Town;

use Dibi\Connection;
use Rendix2\FamilyTree\App\Filters\TownFilter;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultSelector;
use Rendix2\FamilyTree\App\Model\Entities\TownEntity;
use Rendix2\FamilyTree\App\Model\Managers\Town\Interfaces\ITownSelector;

/**
 * Class TownSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Town
 * @method TownFilter getFilter()
 */
class TownSelector extends DefaultSelector implements ITownSelector
{
    /**
     * TownSelector constructor.
     *
     * @param Connection $connection
     * @param TownTable  $table
     * @param TownFilter $townFilter
     */
    public function __construct(
        Connection $connection,
        TownTable $table,
        TownFilter $townFilter
    ) {
        parent::__construct($connection, $table, $townFilter);
    }

    public function getPairsByCountry($countryId)
    {
        $towns = $this->getAllByCountry($countryId);

        return $this->applyFilter($towns);
    }

    public function getAllByCountry($countryId)
    {
        return $this->getAllFluent()
            ->where('[countryId] = %i', $countryId)
            ->execute()
            ->setRowClass(TownEntity::class)
            ->fetchAll();
    }

    public function getToMap()
    {
        return $this->getAllFluent()
            ->where('[gps] IS NOT NULL')
            ->execute()
            ->setRowClass(TownEntity::class)
            ->fetchAll();
    }
}
