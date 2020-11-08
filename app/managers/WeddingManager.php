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
 * Class WeddingManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class WeddingManager extends CrudManager
{
    use RelationDurationManager;

    /**
     * @param int|null $husbandId
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
    public function getAllByWifeId($wifeId)
    {
        return $this->getAllFluent()
            ->where('[wifeId] = %i', $wifeId)
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
     * @return Row[]
     */
    public function getByTownId($townId)
    {
        return $this->getAllFluent()
            ->where('[townId] = %i', $townId)
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
