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
use Rendix2\FamilyTree\App\Model\Entities\TownEntity;

/**
 * Class TownManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class TownManager extends CrudManager
{
    /**
     * @return TownEntity[]
     */
    public function getAll()
    {
        return $this->getAllFluent()->execute()->setRowClass(TownEntity::class)->fetchAll();
    }

    /**
     * @param int $id
     *
     * @return TownEntity
     */
    public function getByPrimaryKey($id)
    {
        return $this->getAllFluent()
            ->where('%n = %i', $this->getPrimaryKey(), $id)
            ->execute()
            ->setRowClass(TownEntity::class)
            ->fetch();
    }

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
     * @return array
     */
    public function getAllPairsCached()
    {
        return $this->getCache()->call([$this, 'getAllPairs']);
    }

    /**
     * @param array $towns
     *
     * @return array
     */
    public function applyTownFilter(array $towns)
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
            ->execute()
            ->setRowClass(TownEntity::class)
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

    /**
     * @return array
     */
    public function getToMap()
    {
        return $this->getAllFluent()
            ->where('[gps] IS NOT NULL')
            ->execute()
            ->setRowClass(TownEntity::class)
            ->fetchAll();
    }
}