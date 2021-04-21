<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonFacadeSelector.php
 * User: Tomáš Babický
 * Date: 10.04.2021
 * Time: 2:43
 */

namespace Rendix2\FamilyTree\App\Model\Facades\Person;

use Dibi\Fluent;
use Nette\NotImplementedException;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Model\Entities\AddressEntity;
use Rendix2\FamilyTree\App\Model\Entities\GenusEntity;
use Rendix2\FamilyTree\App\Model\Entities\PersonEntity;
use Rendix2\FamilyTree\App\Model\Entities\TownEntity;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Model\Facades\DefaultFacade\DefaultFacadeSelector;
use Rendix2\FamilyTree\App\Model\Facades\TownFacade;
use Rendix2\FamilyTree\App\Model\Managers\GenusManager;
use Rendix2\FamilyTree\App\Model\Managers\Person\Interfaces\IPersonSelector;
use Rendix2\FamilyTree\App\Model\Managers\PersonManager;

/**
 * Class PersonFacadeSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Facades\Person
 */
class PersonFacadeSelector extends DefaultFacadeSelector implements IPersonSelector
{
    /**
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * @var GenusManager $genusManager
     */
    private $genusManager;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var TownFacade $townFacade
     */
    private $townFacade;

    /**
     * PersonFacade constructor.
     *
     * @param AddressFacade $addressFacade
     * @param GenusManager  $genusManager
     * @param PersonFilter  $personFilter
     * @param PersonManager $personManager
     * @param TownFacade    $townFacade
     */
    public function __construct(
        AddressFacade $addressFacade,
        GenusManager $genusManager,
        PersonFilter  $personFilter,
        PersonManager $personManager,
        TownFacade $townFacade
    ) {
        parent::__construct($personFilter);

        $this->addressFacade = $addressFacade;
        $this->genusManager = $genusManager;
        $this->personManager = $personManager;
        $this->townFacade = $townFacade;
    }

    public function __destruct()
    {
        $this->addressFacade = null;
        $this->townFacade = null;
        $this->personManager = null;
        $this->genusManager = null;

        parent::__destruct();
    }

    /**
     * @return AddressFacade
     */
    public function getAddressFacade()
    {
        return $this->addressFacade;
    }

    /**
     * @return GenusManager
     */
    public function getGenusManager()
    {
        return $this->genusManager;
    }

    /**
     * @return PersonManager
     */
    public function getPersonManager()
    {
        return $this->personManager;
    }

    /**
     * @return TownFacade
     */
    public function getTownFacade()
    {
        return $this->townFacade;
    }

    /**
     * @param PersonEntity[] $persons
     * @param PersonEntity[] $personParents
     * @param TownEntity[] $towns
     * @param AddressEntity[] $addresses
     * @param GenusEntity[] $genuses
     *
     * @return PersonEntity[]
     */
    protected function join(array $persons, array $personParents, array $towns, array $addresses, array $genuses)
    {
        foreach ($persons as $person) {
            foreach ($towns as $town) {
                if ($person->_birthTownId === $town->id) {
                    $person->birthTown = $town;
                }

                if ($person->_deathTownId === $town->id) {
                    $person->deathTown = $town;
                }

                if ($person->_gravedTownId === $town->id) {
                    $person->gravedTown = $town;
                }
            }

            foreach ($addresses as $address) {
                if ($person->_birthAddressId === $address->id) {
                    $person->birthAddress = $address;
                }

                if ($person->_deathAddressId === $address->id) {
                    $person->deathAddress = $address;
                }

                if ($person->_gravedAddressId === $address->id) {
                    $person->gravedAddress = $address;
                }
            }

            foreach ($personParents as $personParent) {
                if ($person->_motherId === $personParent->id) {
                    $person->mother = $personParent;
                    continue;
                }

                if ($person->_fatherId === $personParent->id) {
                    $person->father = $personParent;
                    continue;
                }
            }

            foreach ($genuses as $genus) {
                if ($person->_genusId === $genus->id) {
                    $person->genus = $genus;
                    break;
                }
            }

            $person->clean();
        }

        return $persons;
    }

    public function getByMotherId($motherId)
    {
        throw new NotImplementedException();
    }

    public function getMalesByMotherId($motherId)
    {
        throw new NotImplementedException();
    }

    public function getFemalesByMotherId($motherId)
    {
        throw new NotImplementedException();
    }

    public function getByFatherId($fatherId)
    {
        throw new NotImplementedException();
    }

    public function getMalesByFatherId($fatherId)
    {
        throw new NotImplementedException();
    }

    public function getFemalesByFatherId($fatherId)
    {
        throw new NotImplementedException();
    }

    /**
     * @param int $genusId
     *
     * @return PersonEntity[]
     */
    public function getByGenusId($genusId)
    {
        $genusPersons = $this->personManager->select()->getManager()->getByGenusId($genusId);
        $persons = $this->personManager->select()->getCachedManager()->getAll();

        $towns = $this->townFacade->select()->getCachedManager()->getAll();
        $addresses = $this->addressFacade->select()->getCachedManager()->getAll();
        $genuses = $this->genusManager->select()->getCachedManager()->getByPrimaryKey($genusId);

        return $this->join($genusPersons, $persons, $towns, $addresses, [$genuses]);
    }

    public function getByBirthTownId($townId)
    {
        throw new NotImplementedException();
    }

    public function getByBirthAddressId($addressId)
    {
        throw new NotImplementedException();
    }

    public function getByDeathTownId($townId)
    {
        throw new NotImplementedException();
    }

    public function getByDeathAddressId($addressId)
    {
        throw new NotImplementedException();
    }

    public function getByGravedTownId($townId)
    {
        throw new NotImplementedException();
    }

    public function getByGravedAddressId($addressId)
    {
        throw new NotImplementedException();
    }

    /**
     * @return PersonEntity[]
     */
    public function getAll()
    {
        $persons = $this->personManager->select()->getCachedManager()->getAll();

        $towns = $this->townFacade->select()->getCachedManager()->getAll();
        $addresses = $this->addressFacade->select()->getCachedManager()->getAll();
        $genuses = $this->genusManager->select()->getCachedManager()->getAll();

        return $this->join($persons, $persons, $towns, $addresses, $genuses);
    }

    public function getMalesPairs()
    {
        throw new NotImplementedException();
    }

    public function getFemalesPairs()
    {
        throw new NotImplementedException();
    }

    public function getBrothers($fatherId, $motherId, $personId)
    {
        throw new NotImplementedException();
    }

    public function getSisters($fatherId, $motherId, $personId)
    {
        throw new NotImplementedException();
    }

    public function getSonsByPerson(PersonEntity $person)
    {
        throw new NotImplementedException();
    }

    public function getSonsById($id)
    {
        throw new NotImplementedException();
    }

    public function getDaughtersByPerson(PersonEntity $person)
    {
        throw new NotImplementedException();
    }

    public function getDaughtersById($id)
    {
        throw new NotImplementedException();
    }

    public function getChildrenByPerson(PersonEntity $person)
    {
        throw new NotImplementedException();
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

    public function getByPrimaryKey($id)
    {
        $person = $this->personManager->select()->getManager()->getByPrimaryKey($id);

        if (!$person) {
            return null;
        }

        $parents = $this->personManager->select()->getManager()->getByPrimaryKeys(
            [
                $person->_motherId,
                $person->_fatherId
            ]
        );

        $towns = $this->townFacade->select()->getManager()->getByPrimaryKeys(
            [
                $person->_birthTownId,
                $person->_deathTownId,
                $person->_gravedTownId,
            ]
        );

        $addresses = $this->addressFacade->select()->getManager()->getByPrimaryKeys(
            [
                $person->_birthAddressId,
                $person->_deathAddressId,
                $person->_gravedAddressId,
            ]
        );

        $genus = [];

        if ($person->_genusId) {
            $genus[] = $this->genusManager->select()->getManager()->getByPrimaryKey($person->_genusId);
        }

        return $this->join([$person], $parents, $towns, $addresses, $genus)[0];
    }

    public function getByPrimaryKeys(array $ids)
    {
        $persons = $this->personManager->select()->getManager()->getByPrimaryKeys($ids);

        if (!$persons) {
            return [];
        }

        $personParentsIds = [];
        $townIds = [];
        $addressIds = [];
        $genusIds = [];

        foreach ($persons as $person) {
            $personParentsIds[] = $person->_motherId;
            $personParentsIds[] = $person->_fatherId;

            $townIds[] = $person->_birthTownId;
            $townIds[] = $person->_deathTownId;
            $townIds[] = $person->_gravedTownId;

            $addressIds[] = $person->_birthAddressId;
            $addressIds[] = $person->_deathAddressId;
            $addressIds[] = $person->_gravedAddressId;

            $genusIds[] = $person->_genusId;
        }

        $townIds = array_unique($townIds);

        $parents = $this->personManager->select()->getManager()->getByPrimaryKeys($personParentsIds);

        foreach ($parents as $parent) {
            $townIds[] = $parent->_birthTownId;
            $townIds[] = $parent->_deathTownId;
            $townIds[] = $parent->_gravedTownId;

            $addressIds[] = $parent->_birthAddressId;
            $addressIds[] = $parent->_deathAddressId;
            $addressIds[] = $parent->_gravedAddressId;

            $genusIds[] = $parent->_genusId;
        }

        $townIds = array_unique($townIds);
        $addressIds = array_unique($addressIds);

        $towns = $this->townFacade->select()->getManager()->getByPrimaryKeys($townIds);
        $addresses = $this->addressFacade->select()->getManager()->getByPrimaryKeys($addressIds);
        $genuses = $this->genusManager->select()->getManager()->getByPrimaryKeys($genusIds);

        return $this->join($persons, $parents, $towns, $addresses, $genuses);
    }

    public function getColumnFluent($column)
    {
        throw new NotImplementedException();
    }

    public function getPairs($column)
    {
        throw new NotImplementedException();
    }

    public function getBySubQuery(Fluent $query)
    {
        $persons = $this->personManager->select()->getManager()->getBySubQuery($query);

        $towns = $this->townFacade->select()->getCachedManager()->getAll();
        $addresses = $this->addressFacade->select()->getCachedManager()->getAll();
        $genuses = $this->genusManager->select()->getCachedManager()->getAll();

        return $this->join($persons, $persons, $towns, $addresses, $genuses);
    }

    public function getAllPairs()
    {
        throw new NotImplementedException();
    }
}
