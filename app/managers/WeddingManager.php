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

/**
 * Class WeddingManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class WeddingManager extends CrudManager
{
    /**
     * @param int $people1Id
     *
     * @return array
     */
    public function getByPeople1Id($people1Id)
    {
        return $this->getAllFluent()
            ->where('[people1_id] = %i', $people1Id)
            ->fetchAll();
    }

    /**
     * @param int $people1Id
     *
     * @return array
     */
    public function getByPeople2Id($people1Id)
    {
        return $this->getAllFluent()
            ->where('[people2_id] = %i', $people1Id)
            ->fetchAll();
    }

}
