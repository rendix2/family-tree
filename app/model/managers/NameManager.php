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
     * @return Row[]
     */
    public function getAllJoinedPerson()
    {
        return $this->dibi
            ->select('CONCAT(p.name, " ", p.surname)')
            ->as('personName')
            ->select('n.*')
            ->from($this->getTableName())
            ->as('n')
            ->innerJoin(Tables::PERSON_TABLE)
            ->as('p')
            ->on('[n.personId] = [p.id]')
            ->fetchAll();
    }

    /**
     * @param int $personId
     *
     * @return Row[]
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
     * @return Row[]
     */
    public function getByPersonIdCached($personId)
    {
        return $this->getCache()->call([$this, 'getByPersonId'], $personId);
    }

    /**
     * @param int $id
     * @param int $personId
     *
     * @return Row
     */
    public function getByPrimaryKeyAndPersonId($id, $personId)
    {
        return $this->getAllFluent()
            ->where('%n = %i', $this->getPrimaryKey(), $id)
            ->where('[personId]= %i', $personId)
            ->fetch();
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
