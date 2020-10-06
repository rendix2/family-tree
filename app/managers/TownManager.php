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

/**
 * Class TownManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class TownManager extends CrudManager
{
    /**
     * @param int $countryId
     * @return array
     */
    public function getPairsByCountry($countryId)
    {
        return $this->getAllFluent()
            ->where('[countryId] = %i', $countryId)
            ->fetchPairs('id', 'name');
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
