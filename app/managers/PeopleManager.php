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

/**
 * Class PeopleManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class PeopleManager extends CrudManager
{
    /**
     * @param int $motherId
     *
     * @return Row[]
     */
    public function getByMotherId($motherId)
    {
        return $this->getAllFluent()
            ->where('[motherId] = %i', $motherId)
            ->fetchAll();
    }

    /**
     * @param int $fatherId
     *
     * @return Row[]
     */
    public function getByFatherId($fatherId)
    {
        return $this->getAllFluent()
            ->where('[fatherId] = %i', $fatherId)
            ->fetchAll();
    }

    /**
     * @param int $genusId
     *
     * @return Row[]
     */
    public function getByGenusId($genusId)
    {
        return $this->getAllFluent()
            ->where('[genusId] = %i', $genusId)
            ->fetchAll();
    }

    /**
     * @param int $motherId
     *
     * @return Result|int
     */
    public function deleteByMotherId($motherId)
    {
        return $this->getAllFluent()
            ->where('[motherId] = %i', $motherId)
            ->execute();
    }

    /**
     * @param int $fatherId
     *
     * @return Result|int
     */
    public function deleteByFatherId($fatherId)
    {
        return $this->getAllFluent()
            ->where('[fatherId] = %i', $fatherId)
            ->execute();
    }

    /**
     * @return array
     */
    public function getAllPairs()
    {
        return $this->dibi
            ->select('id')
            ->select('CONCAT(name, " ", surname)')
            ->as('name')
            ->from($this->getTableName())
            ->fetchPairs('id', 'name');
    }

    /**
     * @return array
     */
    public function getMalesPairs()
    {
        return $this->dibi
            ->select('id')
            ->select('CONCAT(name, " ", surname)')
            ->as('name')
            ->from($this->getTableName())
            ->where('[gender] = %s', 'm')
            ->fetchPairs('id', 'name');
    }

    /**
     * @return array
     */
    public function getFemalesPairs()
    {
        return $this->dibi
            ->select('id')
            ->select('CONCAT(name, " ", surname)')
            ->as('name')
            ->from($this->getTableName())
            ->where('[gender] = %s', 'f')
            ->fetchPairs('id', 'name');
    }

    /**
     * @param int $id
     * @return Row[]
     */
    public function getChildrenByFather($id)
    {
        return $this->getAllFluent()
            ->where('[fatherId] = %i', $id)
            ->fetchAll();
    }

    /**
     * @param int $id
     * @return Row[]
     */
    public function getChildrenByMother($id)
    {
        return $this->getAllFluent()
            ->where('[motherId] = %i', $id)
            ->fetchAll();
    }

    /**
     * @param int $id
     *
     * @return Row[]
     */
    public function getAllExceptMe($id)
    {
        return $this->getAllFluent()
            ->where('[id] != %i', $id)
            ->fetchAll();
    }

    /**
     * @param int|null $fatherId
     * @param int|null $motherId
     * @param int $personId
     *
     * @return Row[]
     */
    public function getBrothers($fatherId, $motherId, $personId)
    {
        $query = $this->getAllFluent();

            if ($fatherId === null) {
                $query->where('[fatherId] IS NULL');
            } else {
                $query->where('[fatherId] = %i', $fatherId);
            }

            if ($motherId === null) {
                $query->where('[motherId] IS NULL');
            } else {
                $query->where('[motherId] = %i', $motherId);
            }

            return $query->where('[id] != %i', $personId)
            ->where('[gender] = %s', 'm')
            ->fetchAll();
    }

    /**
     * @param int|null $fatherId
     * @param int|null $motherId
     * @param int $personId
     *
     * @return Row[]
     */
    public function getSisters($fatherId, $motherId, $personId)
    {
        $query = $this->getAllFluent();

        if ($fatherId === null) {
            $query->where('[fatherId] IS NULL');
        } else {
            $query->where('[fatherId] = %i', $fatherId);
        }

        if ($motherId === null) {
            $query->where('[motherId] IS NULL');
        } else {
            $query->where('[motherId] = %i', $motherId);
        }

        return $query->where('[id] != %i', $personId)
            ->where('[gender] = %s', 'f')
            ->fetchAll();
    }
}
