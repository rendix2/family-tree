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

use Dibi\Result;
use Dibi\Row;

/**
 * Class RelationManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class RelationManager extends CrudManager
{
    use RelationDurationManager;

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
     * @param int $femaleId
     * 
     * @return Row|false
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
