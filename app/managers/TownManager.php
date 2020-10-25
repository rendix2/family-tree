<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownManager.php
 * User: Tomáš Babický
 * Date: 19.09.2020
 * Time: 23:58
 */

namespace Rendix2\FamilyTree\App\Managers;

use Dibi\Row;
use Rendix2\FamilyTree\App\Filters\TownFilter;

/**
 * Class TownManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class TownManager extends CrudManager
{
    /**
     * @param int $countryId
     *
     * @return array
     */
    public function getPairsByCountry($countryId)
    {
        $towns = $this->getAllByCountry($countryId);

        return $this->applyTownFilter($towns);
    }

    /**
     * @return array
     */
    public function getAllPairs()
    {
        $towns = $this->getAll();

        return $this->applyTownFilter($towns);
    }

    /**
     * @param array $towns
     *
     * @return array
     */
    private function applyTownFilter(array $towns)
    {
        $townFilter = new TownFilter();

        $townsResult = [];

        foreach ($towns as $town) {
            $townsResult[$town->id] = $townFilter($town);
        }

        return $townsResult;
    }

    /**
     * @param int $countryId
     *
     * @return Row[]
     */
    public function getAllByCountry($countryId)
    {
        return $this->getAllFluent()
            ->where('[countryId] = %i', $countryId)
            ->fetchAll();
    }

    /**
     * @return Row[]
     */
    public function getAllJoinedCountry()
    {
        return $this->dibi
            ->select('t.*')
            ->select('c.name')
            ->as('countryName')
            ->from($this->getTableName())
            ->as('t')
            ->innerJoin(Tables::COUNTRY_TABLE)
            ->as('c')
            ->on('[t.countryId] = [c.id]')
            ->fetchAll();
    }
}
