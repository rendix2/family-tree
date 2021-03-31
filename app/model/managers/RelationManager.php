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
use Rendix2\FamilyTree\App\Model\Entities\RelationEntity;

/**
 * Class RelationManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class RelationManager extends CrudManager
{
    /**
     * @return RelationEntity[]
     */
    public function getAll()
    {
        return $this->getAllFluent()
            ->execute()
            ->setRowClass(RelationEntity::class)
            ->fetchAll();
    }

    /**
     * @param int $id
     *
     * @return RelationEntity
     */
    public function getByPrimaryKey($id)
    {
        return $this->getAllFluent()
            ->where('%n = %i', $this->getPrimaryKey(), $id)
            ->execute()
            ->setRowClass(RelationEntity::class)
            ->fetch();
    }

    /**
     * @param int $id
     *
     * @return RelationEntity
     */
    public function getByPrimaryKeyCached($id)
    {
        return $this->getCache()->call([$this, 'getByPrimaryKey'], $id);
    }

    /**
     * @param Fluent $query
     *
     * @return RelationEntity[]
     */
    public function getBySubQuery(Fluent $query)
    {
        return $this->getAllFluent()
            ->where('%n in %sql', $this->getPrimaryKey(), $query)
            ->execute()
            ->setRowClass(RelationEntity::class)
            ->fetchAll();
    }

    /**
     * @param int $maleId
     *
     * @return RelationEntity[]
     */
    public function getByMaleId($maleId)
    {
        return $this->getAllFluent()
            ->where('[maleId] = %i', $maleId)
            ->execute()
            ->setRowClass(RelationEntity::class)
            ->fetchAll();
    }

    /**
     * @param int $femaleId
     *
     * @return RelationEntity[]
     */
    public function getByFemaleId($femaleId)
    {
        return $this->getAllFluent()
            ->where('[femaleId] = %i', $femaleId)
            ->execute()
            ->setRowClass(RelationEntity::class)
            ->fetchAll();
    }

    /**
     * @param int $maleId
     * @param int $femaleId
     * 
     * @return RelationEntity|false
     */
    public function getByMaleIdAndFemaleId($maleId, $femaleId)
    {
        return $this->getAllFluent()
            ->where('[maleId] = %i', $maleId)
            ->where('[femaleId] = %i', $femaleId)
            ->fetch();
    }

    /**
     * @param int $maleId
     *
     * @return Result|int
     */
    public function deleteByMaleId($maleId)
    {
        return $this->deleteFluent()
            ->where('[maleId] = %i', $maleId)
            ->execute();
    }

    /**
     * @param int $femaleId
     *
     * @return Result|int
     */
    public function deleteByFemaleId($femaleId)
    {
        return $this->deleteFluent()
            ->where('[femaleId] = %i', $femaleId)
            ->execute();
    }

    /**
     * @param int $maleId
     * @param int $femaleId
     *
     * @return Result|int
     */
    public function deleteByMaleIdAndFemaleId($maleId, $femaleId)
    {
        return $this->deleteFluent()
            ->where('[maleId] = %i', $maleId)
            ->where('[femaleId] = %i', $femaleId)
            ->execute();
    }
}
