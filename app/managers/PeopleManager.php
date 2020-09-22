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

use Dibi\DateTime;
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
     * @param Row $person
     *
     * @return Row[]
     */
    public function getChildrenByPerson(Row $person)
    {
        if ($person->gender === 'm') {
            $children = $this->getByFatherId($person->id);
        } elseif ($person->gender === 'f') {
            $children = $this->getByMotherId($person->id);
        } else {
            throw new Exception('Unknown gender of person.');
        }

        return $children;
    }

    /**
     * @param int $id
     *
     * @return Row[]
     */
    public function getChildrenById($id)
    {
         $person = $this->getByPrimaryKey($id);

         return $this->getChildrenByPerson($person);
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
     * @param int $id
     * @return array
     */
    public function calculateAgeById($id)
    {
        $person = $this->getByPrimaryKey($id);

        return $this->calculateAgeByPerson($person);
    }

    /**
     * @param Row $person
     * @return array
     */
    public function calculateAgeByPerson($person)
    {
        $age = null;
        $accuracy = null;

        if ($person->hasBirthDate) {
            if ($person->stillAlive) {
                $now = new DateTime();

                $diff = $now->diff($person->birthDate);

                $age = $diff->y;
                $accuracy = 1;
            } else {
                if ($person->hasDeathDate) {
                    $diff = $person->deathDate->diff($person->birthDate);
                    $age = $diff->y;

                    $accuracy = 1;
                } elseif ($person->hasDeathYear) {
                    $deathDateTime = new DateTime($person->deathYear);

                    $diff = $deathDateTime->diff($person->birthDate);
                    $age = $diff->y;
                    $accuracy = 3;
                } elseif ($person->hasAge) {
                    $age = $person->age;
                    $accuracy = 2;
                } else {
                    $age = false;
                }
            }
        } else {
            if ($person->hasBirthYear) {
                if ($person->stillAlive) {
                    $now = new DateTime();
                    $birthDateTime = new DateTime($person->birthYear);

                    $diff = $now->diff($birthDateTime);

                    $age = $diff->y;
                } else {
                    if ($person->hasDeathDate) {
                        $deathDateTime = new DateTime($person->deathYear);
                        $birthDateTime = new DateTime($person->birthYear);

                        $diff = $deathDateTime->diff($birthDateTime);

                        $age = $diff->y;
                        $accuracy = 2;
                    } elseif ($person->hasDeathYear) {
                        $age = $person->deathYear - $person->birthYear;
                        $accuracy = 2;
                    } elseif ($person->hasAge) {
                        $age = $person->age;
                        $accuracy = 2;
                    }
                }
            } else {
                $age = false;
            }
        }

        return ['age' => $age, 'accuracy' => $accuracy];
    }
}
