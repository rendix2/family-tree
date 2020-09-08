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

/**
 * Class WeddingManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class WeddingManager extends CrudManager
{
    /**
     * @param int $husbandId
     *
     * @return Row[]
     */
    public function getAllByHusbandId($husbandId)
    {
        return $this->getAllFluent()
            ->where('[husbandId] = %i', $husbandId)
            ->fetchAll();
    }

    /**
     * @param int $wifeId
     *
     * @return Row[]
     */
    public function getAllByHusbandIdJoined($wifeId)
    {
        return $this->getAllFluent()
            ->innerJoin(Tables::PEOPLE_TABLE)
            ->as('p')
            ->on('[p.id] = [wifeId]')
            ->where('[husbandId] = %i', $wifeId)
            ->fetchAll();
    }

    /**
     * @param int $wifeId
     *
     * @return Row[]
     */
    public function getAllByWifeId($wifeId)
    {
        return $this->getAllFluent()
            ->where('[wifeId] = %i', $wifeId)
            ->fetchAll();
    }

    /**
     * @param int $wifeId
     *
     * @return Row[]
     */
    public function getAllByWifeIdJoined($wifeId)
    {
        return $this->getAllFluent()
            ->innerJoin(Tables::PEOPLE_TABLE)
            ->as('p')
            ->on('[p.id] = [husbandId]')
            ->where('[wifeId] = %i', $wifeId)
            ->fetchAll();
    }

    /**
     * @param int $husbandId
     *
     * @return Row|false
     */
    public function getLastByHusbandId($husbandId)
    {
        return $this->getAllFluent()
            ->where('[husbandId] = %i', $husbandId)
            ->orderBy('id', dibi::DESC)
            ->fetch();
    }

    /**
     * @param int $wifeId
     *
     * @return Row|false
     */
    public function getLastByWifeId($wifeId)
    {
        return $this->getAllFluent()
            ->where('[wifeId] = %i', $wifeId)
            ->orderBy('id', dibi::DESC)
            ->fetch();
    }

    /**
     * @return Fluent
     */
    public function getFluentJoinedBothPeople()
    {
        return $this->dibi
            ->select('w.id')
            ->select('w.husbandId')
            ->select('w.wifeId')

            ->select('p1.name')
            ->as('p1name')

            ->select('p1.surname')
            ->as('p1surname')

            ->select('p2.name')
            ->as('p2name')

            ->select('p2.surname')
            ->as('p2surname')

            ->from($this->getTableName())
            ->as('w')

            ->innerJoin(Tables::PEOPLE_TABLE)
            ->as('p1')
            ->on('[w.husbandId] = [p1.id]')

            ->innerJoin(Tables::PEOPLE_TABLE)
            ->as('p2')
            ->on('[w.wifeId] = [p2.id]');
    }

    /**
     * @param int $id
     * @return Result|int
     */
    public function deleteByHusband($id)
    {
        return $this->deleteFluent()
            ->where('[husbandId] = %i', $id)
            ->execute();
    }

    /**
     * @param int $id
     * @return Result|int
     */
    public function deleteByWife($id)
    {
        return $this->deleteFluent()
            ->where('[wifeId] = %i', $id)
            ->execute();
    }
}
