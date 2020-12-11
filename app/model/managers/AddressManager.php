<?php
/**
 *
 * Created by PhpStorm.
 * Filename: s.php
 * User: Tomáš Babický
 * Date: 23.08.2020
 * Time: 15:11
 */

namespace Rendix2\FamilyTree\App\Managers;

use Dibi\Fluent;
use Dibi\Row;
use Rendix2\FamilyTree\App\Model\Entities\AddressEntity;

/**
 * Class AddressManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class AddressManager extends CrudManager
{
    /**
     * @return AddressEntity[]
     */
    public function getAll()
    {
        return $this->getAllFluent()
            ->execute()
            ->setRowClass(AddressEntity::class)
            ->fetchAll();
    }

    /**
     * @param int $id
     *
     * @return AddressEntity
     */
    public function getByPrimaryKey($id)
    {
        return $this->getAllFluent()
            ->where('%n = %i', $this->getPrimaryKey(), $id)
            ->execute()
            ->setRowClass(AddressEntity::class)
            ->fetch();
    }

    /**
     * @param array $ids
     *
     * @return AddressEntity[]|false
     */
    public function getByPrimaryKeys(array $ids)
    {
        $result = $this->checkValues($ids);

        if ($result !== null) {
            return $result;
        }

        return $this->getAllFluent()
            ->where('%n in %in', $this->getPrimaryKey(), $ids)
            ->execute()
            ->setRowClass(AddressEntity::class)
            ->fetchAll();
    }

    /**
     * @param Fluent $query
     *
     * @return AddressEntity[]
     */
    public function getBySubQuery(Fluent $query)
    {
        return $this->getAllFluent()
            ->where('%n in %sql', $this->getPrimaryKey(), $query)
            ->execute()
            ->setRowClass(AddressEntity::class)
            ->fetchAll();
    }

    /**
     * @param int $countryId
     *
     * @return AddressEntity[]
     */
    public function getAllByCountryId($countryId)
    {
        return $this->getAllFluent()
            ->where('[countryId] = %i', $countryId)
            ->execute()
            ->setRowClass(AddressEntity::class)
            ->fetchAll();
    }

    /**
     * @param int $townId
     *
     * @return AddressEntity[]
     */
    public function getByTownId($townId)
    {
        return $this->getAllFluent()
            ->where('[townId] = %i', $townId)
            ->execute()
            ->setRowClass(AddressEntity::class)
            ->fetchAll();
    }

    /**
     * @return AddressEntity[]
     */
    public function getPairsToMap()
    {
        return $this->getAllFluent()
            ->where('[gps] IS NOT NULL')
            ->execute()
            ->setRowClass(AddressEntity::class)
            ->fetchAll();
    }
}
