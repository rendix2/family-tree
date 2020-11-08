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
use Nette\Http\IRequest;
use Nette\Localization\ITranslator;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Settings;

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
     * @param BackupManager $backupManager
     * @param IRequest $request
     */
    public function __construct(Connection $dibi, BackupManager $backupManager, IRequest $request)
    {
        parent::__construct($dibi, $backupManager);

        $this->request = $request;
    }

    /**
     * @return Fluent
     */
    public function getAllFluent()
    {
        $setting = (int)$this->request->getCookie(Settings::SETTINGS_PERSON_ORDERING);

        if ($setting === Settings::PERSON_ORDERING_ID) {
             return parent::getAllFluent()
                 ->orderBy($this->getPrimaryKey());
        } elseif ($setting === Settings::PERSON_ORDERING_NAME) {
            return parent::getAllFluent()
                ->orderBy('name');
        } elseif ($setting === Settings::PERSON_ORDERING_SURNAME) {
            return parent::getAllFluent()
                ->orderBy('surname');
        } elseif ($setting === Settings::PERSON_ORDERING_NAME_SURNAME) {
            return parent::getAllFluent()
                ->orderBy('name')
                ->orderBy('surname');
        } elseif ($setting === Settings::PERSON_ORDERING_SURNAME_NAME) {
            return parent::getAllFluent()
                ->orderBy('surname')
                ->orderBy('name');
        }
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
     * @param int|null $townId
     *
     * @return Row[]
     */
    public function getByBirthTownId($townId)
    {
        if ($townId === null) {
            return $this->getAllFluent()
                ->where('[birthTownId] IS NULL')
                ->fetchAll();
        } else {
            return $this->getAllFluent()
                ->where('[birthTownId] = %i', $townId)
                ->fetchAll();
        }
    }

    /**
     * @param int $addressId
     *
     * @return array
     */
    public function getByBirthAddressId($addressId)
    {
        if ($addressId === null) {
            return $this->getAllFluent()
                ->where('[birthAddressId] IS NULL')
                ->fetchAll();
        } else {
            return $this->getAllFluent()
                ->where('[birthAddressId] = %i', $addressId)
                ->fetchAll();
        }
    }

    /**
     * @param int|null $townId
     *
     * @return Row[]
     */
    public function getByDeathTownId($townId)
    {
        if ($townId === null) {
            return $this->getAllFluent()
                ->where('[deathTownId] IS NULL')
                ->fetchAll();
        } else {
            return $this->getAllFluent()
                ->where('[deathTownId] = %i', $townId)
                ->fetchAll();
        }
    }

    /**
     * @param int $addressId
     *
     * @return array
     */
    public function getByDeathAddressId($addressId)
    {
        if ($addressId === null)
        {
            return $this->getAllFluent()
                ->where('[deathAddressId] IS NULL')
                ->fetchAll();
        } else {
            return $this->getAllFluent()
                ->where('[deathAddressId] = %i', $addressId)
                ->fetchAll();
        }
    }

    /**
     * @param int|null $townId
     *
     * @return Row[]
     */
    public function getByGravedTownId($townId)
    {
        if ($townId === null) {
            return $this->getAllFluent()
                ->where('[gravedTownId] IS NULL')
                ->fetchAll();
        } else {
            return $this->getAllFluent()
                ->where('[gravedTownId] = %i', $townId)
                ->fetchAll();
        }
    }

    /**
     * @param int $addressId
     *
     * @return array
     */
    public function getByGravedAddressId($addressId)
    {
        if ($addressId === null)
        {
            return $this->getAllFluent()
                ->where('[gravedAddressId] IS NULL')
                ->fetchAll();
        } else {
            return $this->getAllFluent()
                ->where('[gravedAddressId] = %i', $addressId)
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

        return $this->applyPersonFilter($persons, $translator);
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

        return $this->applyPersonFilter($persons, $translator);
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

        return $this->applyPersonFilter($persons, $translator);
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
     * @param Row $person
     *
     * @return Row[]
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
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
