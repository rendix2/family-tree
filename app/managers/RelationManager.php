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

use Dibi\Exception;
use Dibi\Fluent;
use Dibi\Result;
use Dibi\Row;

/**
 * Class RelationManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class RelationManager extends CrudManager
{
    /**
     * @param int $maleId
     *
     * @return array
     */
    public function getByMaleId($maleId)
    {
        return $this->getAllFluent()
            ->where('[maleId] = %i', $maleId)
            ->fetchAll();
    }

    /**
     * @param int $maleId
     *
     * @return array
     */
    public function getByMaleIdJoined($maleId)
    {
        return $this->dibi
            ->select('*')
            ->from($this->getTableName())
            ->as($this->getTableAlias())
            ->innerJoin(Tables::PERSON_TABLE)
            ->as('p')
            ->on('[r.femaleId] = [p.id]')
            ->where('[r.maleId] = %i', $maleId)
            ->fetchAll();
    }

    /**
     * @param int $femaleId
     *
     * @return array
     */
    public function getByFemaleId($femaleId)
    {
        return $this->getAllFluent()
            ->where('[femaleId] = %i', $femaleId)
            ->fetchAll();
    }

    /**
     * @param int $femaleId
     *
     * @return array
     */
    public function getByFemaleIdJoined($femaleId)
    {
        return $this->dibi
            ->select('*')
            ->from($this->getTableName())
            ->as($this->getTableAlias())
            ->innerJoin(Tables::PERSON_TABLE)
            ->as('p')
            ->on('[r.maleId] = [p.id]')
            ->where('[r.femaleId] = %i', $femaleId)
            ->fetchAll();
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
     * @return Fluent
     */
    public function getFluentBothJoined()
    {
        return $this->dibi
            ->select('r.id')
            ->select('r.maleId')
            ->select('r.femaleId')

            ->select('p1.name')
            ->as('maleName')

            ->select('p1.surname')
            ->as('maleSurname')

            ->select('p2.name')
            ->as('femaleName')

            ->select('p2.surname')
            ->as('femaleSurname')

            ->from($this->getTableName())
            ->as($this->getTableAlias())

            ->innerJoin(Tables::PERSON_TABLE)
            ->as('p1')
            ->on('[maleId] = p1.id')

            ->innerJoin(Tables::PERSON_TABLE)
            ->as('p2')
            ->on('[femaleId] = p2.id');
    }
}
