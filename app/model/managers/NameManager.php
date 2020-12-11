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

use dibi;
use Dibi\Fluent;
use Dibi\Result;
use Dibi\Row;
use Rendix2\FamilyTree\App\Model\Entities\NameEntity;

/**
 * Class NameManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class NameManager extends CrudManager
{
    /**
     * @return NameEntity[]
     */
    public function getAll()
    {
        return $this->getAllFluent()
            ->execute()
            ->setRowClass(NameEntity::class)
            ->fetchAll();
    }

    /**
     * @param int $id
     *
     * @return NameEntity
     */
    public function getByPrimaryKey($id)
    {
        return $this->getAllFluent()
            ->where('%n = %i', $this->getPrimaryKey(), $id)
            ->execute()
            ->setRowClass(NameEntity::class)
            ->fetch();
    }

    /**
     * @param Fluent $query
     *
     * @return NameEntity[]
     */
    public function getBySubQuery(Fluent $query)
    {
        return $this->getAllFluent()
            ->where('%n in %sql', $this->getPrimaryKey(), $query)
            ->execute()
            ->setRowClass(NameEntity::class)
            ->fetchAll();
    }

    /**
     * @param int $personId
     *
     * @return NameEntity[]
     */
    public function getByPersonId($personId)
    {
        return $this->getAllFluent()
            ->where('[personId] = %i', $personId)
            ->orderBy('dateSince', dibi::ASC)
            ->execute()
            ->setRowClass(NameEntity::class)
            ->fetchAll();
    }

    /**
     * @param int $personId
     *
     * @return NameEntity[]
     */
    public function getByPersonIdCached($personId)
    {
        return $this->getCache()->call([$this, 'getByPersonId'], $personId);
    }

    /**
     * @param int $genusId
     *
     * @return NameEntity[]
     */
    public function getByGenusId($genusId)
    {
        return $this->getAllFluent()
            ->where('[genusId] = %i', $genusId)
            ->execute()
            ->setRowClass(NameEntity::class)
            ->fetchAll();
    }

    /**
     * @param int $personId
     *
     * @return Result|int
     */
    public function deleteByPersonId($personId)
    {
        return $this->deleteFluent()
            ->where('[personId] = %i', $personId)
            ->execute();
    }
}
