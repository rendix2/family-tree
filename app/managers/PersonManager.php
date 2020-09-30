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
use Nette\Localization\ITranslator;
use Rendix2\FamilyTree\App\Filters\PersonFilter;

/**
 * Class PersonManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class PersonManager extends CrudManager
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
     * @param int|null $motherId
     *
     * @return Row[]
     */
    public function getMalesByMotherId($motherId)
    {
        if ($motherId === null) {
            return $this->getAllFluent()
                ->where('[motherId] IS NULL')
                ->where('[gender] = %s', 'm')
                ->fetchAll();
        } else {
            return $this->getAllFluent()
                ->where('[motherId] = %i', $motherId)
                ->where('[gender] = %s', 'm')
                ->fetchAll();
        }
    }

    /**
     * @param int|null $motherId
     *
     * @return Row[]
     */
    public function getFemalesByMotherId($motherId)
    {
        if ($motherId === null) {
            return $this->getAllFluent()
                ->where('[motherId] IS NULL')
                ->where('[gender] = %s', 'f')
                ->fetchAll();
        } else {
            return $this->getAllFluent()
                ->where('[motherId] = %i', $motherId)
                ->where('[gender] = %s', 'f')
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
     * @param int|null $fatherId
     *
     * @return Row[]
     */
    public function getMalesByFatherId($fatherId)
    {
        if ($fatherId === null) {
            return $this->getAllFluent()
                ->where('[fatherId] IS NULL')
                ->where('[gender] = %s', 'm')
                ->fetchAll();
        } else {
            return $this->getAllFluent()
                ->where('[fatherId] = %i', $fatherId)
                ->where('[gender] = %s', 'm')
                ->fetchAll();
        }
    }

    /**
     * @param int|null $fatherId
     *
     * @return Row[]
     */
    public function getFemalesByFatherId($fatherId)
    {
        if ($fatherId === null) {
            return $this->getAllFluent()
                ->where('[fatherId] IS NULL')
                ->where('[gender] = %s', 'f')
                ->fetchAll();
        } else {
            return $this->getAllFluent()
                ->where('[fatherId] = %i', $fatherId)
                ->where('[gender] = %s', 'f')
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
     * @param int $genusId
     * @return Row|false
     */
    public function getFirstOfGenusId($genusId)
    {
        return $this->getAllFluent()
            ->where('[genusId] = %i', $genusId)
            ->where('[motherId] IS NULL')
            ->where('[fatherId] IS NULL')
            ->fetch();
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
     * @param int|null $placeId
     *
     * @return Row[]
     */
    public function getByGravedPlaceId($placeId)
    {
        if ($placeId === null) {
            return $this->getAllFluent()
                ->where('[gravedPlaceId] IS NULL')
                ->fetchAll();
        } else {
            return $this->getAllFluent()
                ->where('[gravedPlaceId] = %i', $placeId)
                ->fetchAll();
        }
    }

    /**
     * @param ITranslator $translator
     * @return array
     */
    public function getAllPairs(ITranslator $translator)
    {
        $persons = $this->getAll();

        $personFilter = new PersonFilter($translator);

        $resultPersons = [];

        foreach ($persons as $person) {
            $resultPersons[$person->id] = $personFilter($person);
        }

        return $resultPersons;
    }

    /**
     * @param ITranslator $translator
     * @return array
     */
    public function getMalesPairs(ITranslator $translator)
    {
        $persons = $this->getAllFluent()
            ->where('[gender] = %s', 'm')
            ->fetchAll();

        $personFilter = new PersonFilter($translator);

        $resultPersons = [];

        foreach ($persons as $person) {
            $resultPersons[$person->id] = $personFilter($person);
        }

        return $resultPersons;
    }

    /**
     * @param ITranslator $translator
     * @return array
     */
    public function getFemalesPairs(ITranslator $translator)
    {
        $persons = $this->getAllFluent()
            ->where('[gender] = %s', 'f')
            ->fetchAll();

        $personFilter = new PersonFilter($translator);

        $resultPersons = [];

        foreach ($persons as $person) {
            $resultPersons[$person->id] = $personFilter($person);
        }

        return $resultPersons;
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
     * @param int $id
     *
     * @return Row[]
     */
    public function getMalesExceptMe($id)
    {
        return $this->getAllFluent()
            ->where('[gender] = %s', 'm')
            ->where('[id] != %i', $id)
            ->fetchAll();
    }

    /**
     * @param int $id
     *
     * @return Row[]
     */
    public function getFemalesExceptMe($id)
    {
        return $this->getAllFluent()
            ->where('[gender] = %s', 'f')
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
     * @return Row[]
     */
    public function getMissingFathers()
    {
        return $this->getAllFluent()
            ->where('[fatherId] IS NULL')
            ->where('[motherId] IS NOT NULL')
            ->fetchAll();
    }

    /**
     * @return Row[]
     */
    public function getMissingMothers()
    {
        return $this->getAllFluent()
            ->where('[motherId] IS NULL')
            ->where('[fatherId] IS NOT NULL')
            ->fetchAll();
    }

    /**
     * @return Row[]
     */
    public function getMissingParents()
    {
        return $this->getAllFluent()
            ->where('[motherId] IS NULL')
            ->where('[fatherId] IS NULL')
            ->fetchAll();
    }


    /**
     * @return Row[]
     */
    public function getMissingBirths()
    {
        return $this->getAllFluent()
            ->where('[birthDate] IS NULL')
            ->where('[birthYear] IS NULL')
            ->fetchAll();
    }

    /**
     * @return Row[]
     */
    public function getMissingDeaths()
    {
        return $this->getAllFluent()
            ->where('[deathDate] IS NULL')
            ->where('[deathYear] IS NULL')
            ->where('[stillAlive] = %i', 0)
            ->fetchAll();
    }

    /**
     * @return Row[]
     */
    public function getMissingDates()
    {
        return $this->getAllFluent()
            ->where('[birthDate] IS NULL')
            ->where('[birthYear] IS NULL')
            ->where('[deathDate] IS NULL')
            ->where('[deathYear] IS NULL')
            ->where('[stillAlive] = %i', 0)
            ->fetchAll();
    }

    /**
     * @param Row $person
     *
     * @return Row[]
     */
    public function getSonsByPerson(Row $person)
    {
        if ($person->gender === 'm') {
            $children = $this->getMalesByFatherId($person->id);
        } elseif ($person->gender === 'f') {
            $children = $this->getMalesByMotherId($person->id);
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
    public function getSonsById($id)
    {
        $person = $this->getByPrimaryKey($id);

        return $this->getSonsByPerson($person);
    }

    /**
     * @param Row $person
     *
     * @return Row[]
     */
    public function getDaughtersByPerson(Row $person)
    {
        if ($person->gender === 'm') {
            $children = $this->getFemalesByFatherId($person->id);
        } elseif ($person->gender === 'f') {
            $children = $this->getFemalesByMotherId($person->id);
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
    public function getDaughtersById($id)
    {
        $person = $this->getByPrimaryKey($id);

        return $this->getDaughtersByPerson($person);
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
        $nowAge = null;
        $accuracy = null;
        $now = new DateTime();
        $nowYear = $now->format('Y');

        if ($person->hasBirthDate && $person->hasDeathDate) {
            $deathYear = (int)$person->deathDate->format('Y');
            $birthYear = (int)$person->birthDate->format('Y');

            if ($birthYear > 1970 && $deathYear > 1970) {
                $diff = $person->deathDate->diff($person->birthDate);
                $nowDiff = $now->diff($person->birthDate);

                $age = $diff->y;
                $nowAge = $nowDiff->y;
                $accuracy = 1;
            } else {
                $age = $deathYear - $birthYear;
                $nowAge = $nowYear - $birthYear;
                $accuracy = 3;
            }
        } elseif ($person->hasDeathYear && $person->hasBirthYear) {
            $age = $person->deathYear - $person->birthYear;
            $nowAge = $nowYear - $person->birthYear;
            $accuracy = 2;
        } elseif ($person->hasDeathDate && $person->hasBirthYear) {

            $deathYear = (int)$person->deathDate->format('Y');

            if ($deathYear > 1970) {
                if ($person->birthYear > 1970) {
                    $birthYearDateTime = new DateTime($person->birthYear);
                    $diff = $person->deathDate->diff($birthYearDateTime);
                    $nowDiff = $now->diff($birthYearDateTime);

                    $age = $diff->y;
                    $nowAge = $nowDiff->y;
                    $accuracy = 2;
                } else {
                    $age = $deathYear - $person->birthYear;
                    $nowAge = $nowYear - $person->birthYear;
                    $accuracy = 3 ;
                }
            } else {
                $age = $deathYear - $person->birthYear;
                $nowAge = $nowYear - $person->birthYear;
                $accuracy = 3 ;
            }
        } elseif ($person->hasDeathYear && $person->hasBirthDate) {
            $birthDate = (int)$person->birthDate->format('Y');

            if ($birthDate > 1970) {
                if ($person->deathYear > 1970) {
                    $deathYearDateTime = new DateTime($person->deathYear);

                    $diff = $deathYearDateTime->diff($person->birthDate);
                    $nowDiff = $now->diff($person->birthDate);

                    $age = $diff->y;
                    $nowAge = $nowDiff->y;
                    $accuracy = 2;
                } else {
                    $age = $person->deathYear - $person->birthYear;
                    $nowAge = $nowYear - $person->birthYear;
                    $accuracy = 3 ;
                }
            } else {
                $age = $person->deathYear - $birthDate;
                $nowAge = $nowYear - $birthDate;
                $accuracy = 3 ;
            }
        } elseif ($person->stillAlive) {
            if ($person->hasBirthDate) {
                $birthYear = $person->birthDate->format('Y');

                if ($birthYear > 1970) {
                    $diff = $now->diff($person->birthDate);

                    $age = $diff->y;
                    $accuracy = 1;
                } else {
                    $now = new DateTime();
                    $nowYear = $now->format('Y');

                    $age = $nowYear - $birthYear;
                    $accuracy = 2;
                }
            } elseif($person->hasBirthYear) {
                if ($person->hasBirthYear > 1970) {
                    $birthYearDateTime = new DateTime($person->birthYear);

                    $diff = $now->diff($birthYearDateTime);

                    $age = $diff->y;
                    $accuracy = 1;
                } else {
                    $age = $nowYear - $person->birthYear;
                    $accuracy = 2;
                }
            }
        } elseif ($person->hasAge) {
            $age = $person->age;

            $accuracy = 1;
        }

        return ['age' => $age, 'accuracy' => $accuracy, 'nowAge' => $nowAge];
    }
}
