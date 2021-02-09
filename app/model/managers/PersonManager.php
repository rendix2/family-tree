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

use Dibi\Connection;
use Dibi\DateTime;
use Dibi\Fluent;
use Dibi\Result;
use Dibi\Row;
use Exception;
use Nette\Caching\IStorage;
use Nette\Http\IRequest;
use Nette\Localization\ITranslator;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Model\Entities\PersonEntity;
use Rendix2\FamilyTree\SettingsModule\App\Presenters\PersonPresenter;

/**
 * Class PersonManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class PersonManager extends CrudManager
{
    /**
     * @var IRequest $request
     */
    private $request;

    /**
     * PersonManager constructor.
     *
     * @param Connection $dibi
     * @param IRequest $request
     * @param IStorage $storage
     */
    public function __construct(
        Connection $dibi,
        IRequest $request,
        IStorage $storage
    ) {
        parent::__construct($dibi, $storage);

        $this->request = $request;
    }

    /**
     * @return Fluent
     */
    public function getAllFluent()
    {
        $setting = (int)$this->request->getCookie(PersonPresenter::SETTINGS_PERSON_ORDERING);

        if ($setting === PersonPresenter::PERSON_ORDERING_ID) {
             return parent::getAllFluent()
                 ->orderBy($this->getPrimaryKey());
        } elseif ($setting === PersonPresenter::PERSON_ORDERING_NAME) {
            return parent::getAllFluent()
                ->orderBy('name');
        } elseif ($setting === PersonPresenter::PERSON_ORDERING_SURNAME) {
            return parent::getAllFluent()
                ->orderBy('surname');
        } elseif ($setting === PersonPresenter::PERSON_ORDERING_NAME_SURNAME) {
            return parent::getAllFluent()
                ->orderBy('name')
                ->orderBy('surname');
        } elseif ($setting === PersonPresenter::PERSON_ORDERING_SURNAME_NAME) {
            return parent::getAllFluent()
                ->orderBy('surname')
                ->orderBy('name');
        } else {
            return parent::getAllFluent()
                ->orderBy($this->getPrimaryKey());
        }
    }

    /**
     * @return PersonEntity[]
     */
    public function getAll()
    {
        return $this->getAllFluent()
            ->execute()
            ->setRowClass(PersonEntity::class)
            ->fetchAll();
    }

    /**
     * @return PersonEntity[]
     */
    public function getAllCached()
    {
        return $this->getCache()->call([$this, 'getAll']);
    }

    /**
     * @param int $id
     *
     * @return PersonEntity
     */
    public function getByPrimaryKey($id)
    {
        return $this->getAllFluent()
            ->where('%n = %i', $this->getPrimaryKey(), $id)
            ->execute()
            ->setRowClass(PersonEntity::class)
            ->fetch();
    }

    /**
     * @param array $ids
     *
     * @return PersonEntity[]
     */
    public function getByPrimaryKeys(array $ids)
    {
        return $this->getAllFluent()
            ->where('%n in %in', $this->getPrimaryKey(), $ids)
            ->execute()
            ->setRowClass(PersonEntity::class)
            ->fetchAll();
    }

    /**
     * @param Fluent $query
     *
     * @return PersonEntity[]
     */
    public function getBySubQuery(Fluent $query)
    {
        return $this->getAllFluent()
            ->where('%n in %sql', $this->getPrimaryKey(), $query)
            ->execute()
            ->setRowClass(PersonEntity::class)
            ->fetchAll();
    }

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
                ->execute()
                ->setRowClass(PersonEntity::class)
                ->fetchAll();
        } else {
            return $this->getAllFluent()
                ->where('[motherId] = %i', $motherId)
                ->where('[gender] = %s', 'm')
                ->execute()
                ->setRowClass(PersonEntity::class)
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
                ->execute()
                ->setRowClass(PersonEntity::class)
                ->fetchAll();
        } else {
            return $this->getAllFluent()
                ->where('[motherId] = %i', $motherId)
                ->where('[gender] = %s', 'f')
                ->execute()
                ->setRowClass(PersonEntity::class)
                ->fetchAll();
        }
    }

    /**
     * @param int|null $fatherId
     *
     * @return PersonEntity[]
     */
    public function getByFatherId($fatherId)
    {
        if ($fatherId === null) {
            return $this->getAllFluent()
                ->where('[fatherId] IS NULL')
                ->execute()
                ->setRowClass(PersonEntity::class)
                ->fetchAll();
        } else {
            return $this->getAllFluent()
                ->where('[fatherId] = %i', $fatherId)
                ->execute()
                ->setRowClass(PersonEntity::class)
                ->fetchAll();
        }
    }

    /**
     * @param int|null $fatherId
     *
     * @return PersonEntity[]
     */
    public function getMalesByFatherId($fatherId)
    {
        if ($fatherId === null) {
            return $this->getAllFluent()
                ->where('[fatherId] IS NULL')
                ->where('[gender] = %s', 'm')
                ->execute()
                ->setRowClass(PersonEntity::class)
                ->fetchAll();
        } else {
            return $this->getAllFluent()
                ->where('[fatherId] = %i', $fatherId)
                ->where('[gender] = %s', 'm')
                ->execute()
                ->setRowClass(PersonEntity::class)
                ->fetchAll();
        }
    }

    /**
     * @param int|null $fatherId
     *
     * @return PersonEntity[]
     */
    public function getFemalesByFatherId($fatherId)
    {
        if ($fatherId === null) {
            return $this->getAllFluent()
                ->where('[fatherId] IS NULL')
                ->where('[gender] = %s', 'f')
                ->execute()
                ->setRowClass(PersonEntity::class)
                ->fetchAll();
        } else {
            return $this->getAllFluent()
                ->where('[fatherId] = %i', $fatherId)
                ->where('[gender] = %s', 'f')
                ->execute()
                ->setRowClass(PersonEntity::class)
                ->fetchAll();
        }
    }

    /**
     * @param int|null $genusId
     *
     * @return PersonEntity[]
     */
    public function getByGenusId($genusId)
    {
        if ($genusId === null) {
            return $this->getAllFluent()
                ->where('[genusId] IS NULL')
                ->execute()
                ->setRowClass(PersonEntity::class)
                ->fetchAll();
        } else {
            return $this->getAllFluent()
                ->where('[genusId] = %i', $genusId)
                ->execute()
                ->setRowClass(PersonEntity::class)
                ->fetchAll();
        }
    }

    /**
     * @param int|null $genusId
     *
     * @return PersonEntity[]
     */
    public function getByGenusIdCached($genusId)
    {
        return $this->getCache()->call([$this, 'getByGenusId'], $genusId);
    }

    /**
     * @param int|null $townId
     *
     * @return PersonEntity[]
     */
    public function getByBirthTownId($townId)
    {
        if ($townId === null) {
            return $this->getAllFluent()
                ->where('[birthTownId] IS NULL')
                ->execute()
                ->setRowClass(PersonEntity::class)
                ->fetchAll();
        } else {
            return $this->getAllFluent()
                ->where('[birthTownId] = %i', $townId)
                ->execute()
                ->setRowClass(PersonEntity::class)
                ->fetchAll();
        }
    }

    /**
     * @param int $addressId
     *
     * @return PersonEntity[]
     */
    public function getByBirthAddressId($addressId)
    {
        if ($addressId === null) {
            return $this->getAllFluent()
                ->where('[birthAddressId] IS NULL')
                ->execute()
                ->setRowClass(PersonEntity::class)
                ->fetchAll();
        } else {
            return $this->getAllFluent()
                ->where('[birthAddressId] = %i', $addressId)
                ->execute()
                ->setRowClass(PersonEntity::class)
                ->fetchAll();
        }
    }

    /**
     * @param int $addressId
     *
     * @return PersonEntity[]
     */
    public function getByBirthAddressIdCached($addressId)
    {
        return $this->getCache()->call([$this, 'getByBirthAddressId'], $addressId);
    }

    /**
     * @param int|null $townId
     *
     * @return PersonEntity[]
     */
    public function getByDeathTownId($townId)
    {
        if ($townId === null) {
            return $this->getAllFluent()
                ->where('[deathTownId] IS NULL')
                ->execute()
                ->setRowClass(PersonEntity::class)
                ->fetchAll();
        } else {
            return $this->getAllFluent()
                ->where('[deathTownId] = %i', $townId)
                ->execute()
                ->setRowClass(PersonEntity::class)
                ->fetchAll();
        }
    }

    /**
     * @param int $addressId
     *
     * @return PersonEntity[]
     */
    public function getByDeathAddressId($addressId)
    {
        if ($addressId === null)
        {
            return $this->getAllFluent()
                ->where('[deathAddressId] IS NULL')
                ->execute()
                ->setRowClass(PersonEntity::class)
                ->fetchAll();
        } else {
            return $this->getAllFluent()
                ->where('[deathAddressId] = %i', $addressId)
                ->execute()
                ->setRowClass(PersonEntity::class)
                ->fetchAll();
        }
    }

    /**
     * @param int $addressId
     *
     * @return PersonEntity[]
     */
    public function getByDeathAddressIdCached($addressId)
    {
        return $this->getCache()->call([$this, 'getByDeathAddressId'], $addressId);
    }

    /**
     * @param int|null $townId
     *
     * @return PersonEntity[]
     */
    public function getByGravedTownId($townId)
    {
        if ($townId === null) {
            return $this->getAllFluent()
                ->where('[gravedTownId] IS NULL')
                ->execute()
                ->setRowClass(PersonEntity::class)
                ->fetchAll();
        } else {
            return $this->getAllFluent()
                ->where('[gravedTownId] = %i', $townId)
                ->execute()
                ->setRowClass(PersonEntity::class)
                ->fetchAll();
        }
    }

    /**
     * @param int $addressId
     *
     * @return PersonEntity[]
     */
    public function getByGravedAddressId($addressId)
    {
        if ($addressId === null)
        {
            return $this->getAllFluent()
                ->where('[gravedAddressId] IS NULL')
                ->execute()
                ->setRowClass(PersonEntity::class)
                ->fetchAll();
        } else {
            return $this->getAllFluent()
                ->where('[gravedAddressId] = %i', $addressId)
                ->execute()
                ->setRowClass(PersonEntity::class)
                ->fetchAll();
        }
    }

    /**
     * @param int $addressId
     *
     * @return PersonEntity[]
     */
    public function getByGravedAddressIdCached($addressId)
    {
        return $this->getCache()->call([$this, 'getByGravedAddressId'], $addressId);
    }

    /**
     * @param ITranslator $translator
     *
     * @return array
     */
    public function getAllPairs(ITranslator $translator)
    {
        $persons = $this->getAll();

        return $this->applyPersonFilter($persons, $translator);
    }

    /**
     * @param ITranslator $translator
     * @return array
     */
    public function getAllPairsCached(ITranslator $translator)
    {
        return $this->getCache()->call([$this, 'getAllPairs'], $translator);
    }

    /**
     * @param ITranslator $translator
     *
     * @return array
     */
    public function getMalesPairs(ITranslator $translator)
    {
        $persons = $this->getAllFluent()
            ->where('[gender] = %s', 'm')
            ->execute()
            ->setRowClass(PersonEntity::class)
            ->fetchAll();

        return $this->applyPersonFilter($persons, $translator);
    }

    /**
     * @param ITranslator $translator
     *
     * @return array
     */
    public function getMalesPairsCached(ITranslator $translator)
    {
        return $this->getCache()->call([$this, 'getMalesPairs'], $translator);
    }

    /**
     * @param ITranslator $translator
     *
     * @return array
     */
    public function getFemalesPairs(ITranslator $translator)
    {
        $persons = $this->getAllFluent()
            ->where('[gender] = %s', 'f')
            ->execute()
            ->setRowClass(PersonEntity::class)
            ->fetchAll();

        return $this->applyPersonFilter($persons, $translator);
    }

    /**
     * @param ITranslator $translator
     * @return array
     */
    public function getFemalesPairsCached(ITranslator $translator)
    {
        return $this->getCache()->call([$this, 'getFemalesPairs'], $translator);
    }

    /**
     * @param array $persons
     * @param ITranslator $translator
     *
     * @return array
     */
    public function applyPersonFilter(array $persons, ITranslator $translator)
    {
        $personFilter = new PersonFilter($translator, $this->request);

        $resultPersons = [];

        foreach ($persons as $person) {
            $resultPersons[$person->id] = $personFilter($person);
        }

        return $resultPersons;
    }

    /**
     * @param int|null $fatherId
     * @param int|null $motherId
     * @param int $personId
     *
     * @return PersonEntity[]
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
            ->execute()
            ->setRowClass(PersonEntity::class)
            ->fetchAll();
    }

    /**
     * @param int|null $fatherId
     * @param int|null $motherId
     * @param int $personId
     *
     * @return PersonEntity[]
     */
    public function getBrothersCached($fatherId, $motherId, $personId)
    {
        return $this->getCache()->call([$this, 'getBrothers'], $fatherId, $motherId, $personId);
    }

    /**
     * @param int|null $fatherId
     * @param int|null $motherId
     * @param int $personId
     *
     * @return PersonEntity[]
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
            ->execute()
            ->setRowClass(PersonEntity::class)
            ->fetchAll();
    }

    /**
     * @param int|null $fatherId
     * @param int|null $motherId
     * @param int $personId
     *
     * @return PersonEntity[]
     */
    public function getSistersCached($fatherId, $motherId, $personId)
    {
        return $this->getCache()->call([$this, 'getSisters'], $fatherId, $motherId, $personId);
    }

    /**
     * @param PersonEntity $person
     *
     * @return PersonEntity[]
     * @throws Exception
     */
    public function getSonsByPerson(PersonEntity $person)
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
     * @param PersonEntity $person
     *
     * @return Row[]
     * @throws Exception
     */
    public function getSonsByPersonCached(PersonEntity $person)
    {
        return $this->getCache()->call([$this, 'getSonsByPerson'], $person);
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
     * @param PersonEntity $person
     *
     * @return Row[]
     * @throws Exception
     */
    public function getDaughtersByPerson(PersonEntity $person)
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

    public function getDaughtersByPersonCached(PersonEntity $person)
    {
        return $this->getCache()->call([$this, 'getDaughtersByPerson'], $person);
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
     * @param PersonEntity $person
     *
     * @return Row[]
     * @throws Exception
     */
    public function getChildrenByPerson(PersonEntity $person)
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
     * @param PersonEntity $person
     * @return array
     */
    public function calculateAgeByPerson(PersonEntity $person)
    {
        $age = null;
        $nowAge = null;
        $yearsAfterDeath= null;
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
                $yearsAfterDeath = $nowAge - $age;
                $accuracy = 1;
            } else {
                $age = $deathYear - $birthYear;
                $nowAge = $nowYear - $birthYear;
                $yearsAfterDeath = $nowAge - $age;
                $accuracy = 3;
            }
        } elseif ($person->hasDeathYear && $person->hasBirthYear) {
            $age = $person->deathYear - $person->birthYear;
            $nowAge = $nowYear - $person->birthYear;
            $yearsAfterDeath = $nowAge - $age;
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
                    $yearsAfterDeath = $nowAge - $age;
                    $accuracy = 2;
                } else {
                    $age = $deathYear - $person->birthYear;
                    $nowAge = $nowYear - $person->birthYear;
                    $yearsAfterDeath = $nowAge - $age;
                    $accuracy = 3 ;
                }
            } else {
                $age = $deathYear - $person->birthYear;
                $nowAge = $nowYear - $person->birthYear;
                $yearsAfterDeath = $nowAge - $age;
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
                    $yearsAfterDeath = $nowAge - $age;
                    $accuracy = 2;
                } else {
                    $age = $person->deathYear - $person->birthYear;
                    $nowAge = $nowYear - $person->birthYear;
                    $yearsAfterDeath = $nowAge - $age;
                    $accuracy = 3 ;
                }
            } else {
                $age = $person->deathYear - $birthDate;
                $nowAge = $nowYear - $birthDate;
                $yearsAfterDeath = $nowAge - $age;
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

        return [
            'age' => $age,
            'accuracy' => $accuracy,
            'nowAge' => $nowAge,
            'yearsAfterDeath' => $yearsAfterDeath
        ];
    }
}
