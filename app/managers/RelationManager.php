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
use Dibi\Result;

/**
 * Class RelationManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class RelationManager extends CrudManager
{
    /**
     * @param int $people1Id
     *
     * @return array
     */
    public function getByPeopleId($people1Id)
    {
        return $this->getAllFluent()
            ->where('[people1_id] = %i', $people1Id)
            ->fetchAll();
    }

    /**
     * @param int $people2Id
     *
     * @return array
     */
    public function getByPeople1Id($people2Id)
    {
        return $this->getAllFluent()
            ->where('[people2_id] = %i', $people2Id)
            ->fetchAll();
    }

    /**
     * @param int $people1Id
     *
     * @return Result|int
     * @throws Exception
     */
    public function deleteByPeople1Id($people1Id)
    {
        return $this->deleteFluent()
            ->where('[people1_id] = %i', $people1Id)
            ->execute();
    }

    /**
     * @param int $people2Id
     *
     * @return Result|int
     * @throws Exception
     */
    public function deleteByPeople2Id($people2Id)
    {
        return $this->deleteFluent()
            ->where('[people2_id] = %i', $people2Id)
            ->execute();
    }
}
