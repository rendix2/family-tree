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
use Dibi\Result;
use Dibi\Row;
use Rendix2\FamilyTree\App\Model\Entities\WeddingEntity;

/**
 * Class WeddingManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class WeddingManager extends CrudManager
{
    /**
     * @return WeddingEntity[]
     */
    public function getAll()
    {
        return $this->getAllFluent()
            ->execute()
            ->setRowClass(WeddingEntity::class)
            ->fetchAll();
    }

    /**
     * @param int $id
     *
     * @return WeddingEntity
     */
    public function getByPrimaryKey($id)
    {
        return $this->getAllFluent()
            ->where('%n = %i', $this->getPrimaryKey(), $id)
            ->execute()
            ->setRowClass(WeddingEntity::class)
            ->fetch();
    }

    /**
     * @param int $id
     *
     * @return WeddingEntity
     */
    public function getByPrimaryKeyCached($id)
    {
        return $this->getCache()->call([$this, 'getByPrimaryKey'], $id);
    }

    /**
     * @param Fluent $query
     *
     * @return WeddingEntity[]
     */
    public function getBySubQuery(Fluent $query)
    {
        return $this->getAllFluent()
            ->where('%n in %sql', $this->getPrimaryKey(), $query)
            ->execute()
            ->setRowClass(WeddingEntity::class)
            ->fetchAll();
    }

    /**
     * @param int|null $husbandId
     *
     * @return WeddingEntity[]
     */
    public function getAllByHusbandId($husbandId)
    {
        return $this->getAllFluent()
            ->where('[husbandId] = %i', $husbandId)
            ->execute()
            ->setRowClass(WeddingEntity::class)
            ->fetchAll();
    }

    /**
     * @param int $wifeId
     *
     * @return WeddingEntity[]
     */
    public function getAllByWifeId($wifeId)
    {
        return $this->getAllFluent()
            ->where('[wifeId] = %i', $wifeId)
            ->execute()
            ->setRowClass(WeddingEntity::class)
            ->fetchAll();
    }

    /**
     * @param int $wifeId
     * @param int $husbandId
     *
     * @return Row|false
     */
    public function getByWifeIdAndHusbandId($wifeId, $husbandId)
    {
        return $this->getAllFluent()
            ->where('[wifeId] = %i', $wifeId)
            ->where('[husbandId] = %i', $husbandId)
            ->fetch();
    }

    /**
     * @param int $townId
     *
     * @return WeddingEntity[]
     */
    public function getByTownId($townId)
    {
        return $this->getAllFluent()
            ->where('[townId] = %i', $townId)
            ->execute()
            ->setRowClass(WeddingEntity::class)
            ->fetchAll();
    }


    /**
     * @param int $addressId
     *
     * @return WeddingEntity[]
     */
    public function getByAddressId($addressId)
    {
        return $this->getAllFluent()
            ->where('[addressId] = %i', $addressId)
            ->execute()
            ->setRowClass(WeddingEntity::class)
            ->fetchAll();
    }

    /**
     * @param int $id
     *
     * @return Result|int
     */
    public function deleteByHusbandId($id)
    {
        return $this->deleteFluent()
            ->where('[husbandId] = %i', $id)
            ->execute();
    }

    /**
     * @param int $id
     *
     * @return Result|int
     */
    public function deleteByWifeId($id)
    {
        return $this->deleteFluent()
            ->where('[wifeId] = %i', $id)
            ->execute();
    }

    /**
     * @param int $husbandId
     * @param int $wifeId
     *
     * @return Result|int
     */
    public function deleteByHusbandIdAndWifeId($husbandId, $wifeId)
    {
        return $this->deleteFluent()
            ->where('[husbandId] = %i', $husbandId)
            ->where('[wifeId] = %i', $wifeId)
            ->execute();
    }
}
