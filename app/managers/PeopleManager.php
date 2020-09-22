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
     * @param int|null $motherId
     *
     * @return Row[]
     */
    public function getByMotherId($motherId)
    {
        if ($motherId === null) {
            return $this->getAllFluent()
                ->where('[motherId] IS NULL')
                ->fetchAll();
        } else {
            return $this->getAllFluent()
                ->where('[motherId] = %i', $motherId)
                ->fetchAll();
        }
    }

    /**
     * @param int|null $fatherId
     *
     * @return Row[]
     */
    public function getByFatherId($fatherId)
    {
        if ($fatherId === null) {
            return $this->getAllFluent()
                ->where('[fatherId] IS NULL')
                ->fetchAll();
        } else {
            return $this->getAllFluent()
                ->where('[fatherId] = %i', $fatherId)
                ->fetchAll();
        }
    }

    /**
     * @param int|null $genusId
     *
     * @return Row[]
     */
    public function getByGenusId($genusId)
    {
        if ($genusId === null) {
            return $this->getAllFluent()
                ->where('[genusId] IS NULL')
                ->fetchAll();
        } else {
            return $this->getAllFluent()
                ->where('[genusId] = %i', $genusId)
                ->fetchAll();
        }
    }

    /**
     * @param int|null $placeId
     *
     * @return Row[]
     */
    public function getByBirthPlaceId($placeId)
    {
        if ($placeId === null) {
            return $this->getAllFluent()
                ->where('[birthPlaceId] IS NULL')
                ->fetchAll();
        } else {
            return $this->getAllFluent()
                ->where('[birthPlaceId] = %i', $placeId)
                ->fetchAll();
        }
    }

    /**
     * @param int|null $placeId
     *
     * @return Row[]
     */
    public function getByDeathPlaceId($placeId)
    {
        if ($placeId === null) {
            return $this->getAllFluent()
                ->where('[deathPlaceId] IS NULL')
                ->fetchAll();
        } else {
            return $this->getAllFluent()
                ->where('[deathPlaceId] = %i', $placeId)
                ->fetchAll();
        }
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

    /**
     * @return Row[]
     */
    public function getMissingWeddings()
    {
        return $this->dibi->select('*')
            ->from($this->getTableName())
            ->where('id NOT IN',

                $this->dibi->select('husbandId')
                ->from(Tables::WEDDING_TABLE)
                )
            ->where('id NOT IN',

                $this->dibi->select('wifeId')
                    ->from(Tables::WEDDING_TABLE)
            )
            ->fetchAll();
    }

    /**
     * @return Row[]
     */
    public function getMissingRelations()
    {
        return $this->dibi->select('*')
            ->from($this->getTableName())
            ->where('id NOT IN',

                $this->dibi->select('maleId')
                    ->from(Tables::RELATION_TABLE)
            )
            ->where('id NOT IN',

                $this->dibi->select('femaleId')
                    ->from(Tables::RELATION_TABLE)
            )
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
}
