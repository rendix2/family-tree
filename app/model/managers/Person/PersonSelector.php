<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonSelector.php
 * User: Tomáš Babický
 * Date: 03.04.2021
 * Time: 1:56
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Person;

use Dibi\Connection;
use Exception;
use Nette\NotImplementedException;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultSelector;
use Rendix2\FamilyTree\App\Model\Entities\PersonEntity;
use Rendix2\FamilyTree\App\Model\Managers\Person\Interfaces\IPersonSelector;

/**
 * Class PersonSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Person
 */
class PersonSelector extends DefaultSelector implements IPersonSelector
{
    /**
     * PersonSelector constructor.
     *
     * @param Connection   $connection
     * @param PersonTable  $table
     * @param PersonFilter $personFilter
     */
    public function __construct(
        Connection $connection,
        PersonTable $table,
        PersonFilter $personFilter
    ) {
        parent::__construct($connection, $table, $personFilter);
    }

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

    public function getByDeathAddressId($addressId)
    {
        if ($addressId === null) {
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

    public function getByGravedAddressId($addressId)
    {
        if ($addressId === null) {
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

    public function getMalesPairs()
    {
        $persons = $this->getAllFluent()
            ->where('[gender] = %s', 'm')
            ->execute()
            ->setRowClass(PersonEntity::class)
            ->fetchAll();

        return $this->applyFilter($persons);
    }

    public function getFemalesPairs()
    {
        $persons = $this->getAllFluent()
            ->where('[gender] = %s', 'f')
            ->execute()
            ->setRowClass(PersonEntity::class)
            ->fetchAll();

        return $this->applyFilter($persons);
    }

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

    public function getSonsById($id)
    {
        throw new NotImplementedException();
    }

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

    public function getDaughtersById($id)
    {
        throw new NotImplementedException();
    }

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

    public function getChildrenById($id)
    {
        throw new NotImplementedException();
    }

    public function calculateAgeById($id)
    {
        throw new NotImplementedException();
    }

    public function calculateAgeByPerson(PersonEntity $person)
    {
        throw new NotImplementedException();
    }
}