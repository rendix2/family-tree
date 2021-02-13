<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonFacade.php
 * User: Tomáš Babický
 * Date: 10.11.2020
 * Time: 2:42
 */

namespace Rendix2\FamilyTree\App\Facades;

use Dibi\Fluent;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Managers\GenusManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Model\Entities\AddressEntity;
use Rendix2\FamilyTree\App\Model\Entities\GenusEntity;
use Rendix2\FamilyTree\App\Model\Entities\PersonEntity;
use Rendix2\FamilyTree\App\Model\Entities\TownEntity;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Model\Facades\TownFacade;

/**
 * Class PersonFacade
 *
 * @package Rendix2\FamilyTree\App\Facades
 */
class PersonFacade
{
    /**
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * @var Cache $cache
     */
    private $cache;

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
     * @param IStorage $storage
     * @param GenusManager $genusManager
     * @param PersonManager $personManager
     * @param TownFacade $townFacade
     */
    public function __construct(
        AddressFacade $addressFacade,
        IStorage $storage,
        GenusManager $genusManager,
        PersonManager $personManager,
        TownFacade $townFacade
    ) {
        $this->addressFacade = $addressFacade;
        $this->cache = new Cache($storage, self::class);
        $this->genusManager = $genusManager;
        $this->personManager = $personManager;
        $this->townFacade = $townFacade;
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

    /**
     * @return PersonEntity[]
     */
    public function getAllNotJoined()
    {
        return $this->personManager->getAll();
    }

    /**
     * @return PersonEntity[]
     */
    public function getAll()
    {
        $persons = $this->personManager->getAll();

        $towns = $this->townFacade->getAll();
        $addresses = $this->addressFacade->getAll();
        $genuses = $this->genusManager->getAll();

        return $this->join($persons, $persons, $towns, $addresses, $genuses);
    }

    /**
     * @return PersonEntity[]
     */
    public function getAllCached()
    {
        return $this->cache->call([$this, 'getAll']);
    }

    /**
     * @param int $personId
     *
     * @return PersonEntity
     */
    public function getByPrimaryKey($personId)
    {
        $person = $this->personManager->getByPrimaryKey($personId);

        if (!$person) {
            return null;
        }

        $parents = $this->personManager->getByPrimaryKeys(
            [
                $person->_motherId,
                $person->_fatherId
            ]
        );

        $towns = $this->townFacade->getByPrimaryKeys(
            [
                $person->_birthTownId,
                $person->_deathTownId,
                $person->_gravedTownId,
            ]
        );

        $addresses = $this->addressFacade->getByPrimaryKeys(
            [
                $person->_birthAddressId,
                $person->_deathAddressId,
                $person->_gravedAddressId,
            ]
        );

        $genus = [];

        if ($person->_genusId) {
            $genus[] = $this->genusManager->getByPrimaryKey($person->_genusId);
        }

        return $this->join([$person], $parents, $towns, $addresses, $genus)[0];
    }

    /**
     * @param int $personId
     *
     * @return PersonEntity
     */
    public function getByPrimaryKeyCached($personId)
    {
        return $this->cache->call([$this, 'getByPrimaryKey'], $personId);
    }

    /**
     * @param array $personIds
     *
     * @return PersonEntity[]
     */
    public function getByPrimaryKeys(array $personIds)
    {
        $persons = $this->personManager->getByPrimaryKeys($personIds);

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

        $parents = $this->personManager->getByPrimaryKeys($personParentsIds);

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

        $towns = $this->townFacade->getByPrimaryKeys($townIds);
        $addresses = $this->addressFacade->getByPrimaryKeys($addressIds);
        $genuses = $this->genusManager->getByPrimaryKeys($genusIds);

        return $this->join($persons, $parents, $towns, $addresses, $genuses);
    }

    /**
     * @param array $personId
     *
     * @return PersonEntity[]
     */
    public function getByPrimaryKeysCached($personId)
    {
        return $this->cache->call([$this, 'getByPrimaryKeys'], $personId);
    }

    /**
     * @param int $genusId
     *
     * @return PersonEntity[]
     */
    public function getByGenusId($genusId)
    {
        $genusPersons = $this->personManager->getByGenusId($genusId);
        $persons = $this->personManager->getAll();

        $towns = $this->townFacade->getAll();
        $addresses = $this->addressFacade->getAll();
        $genuses = $this->genusManager->getByPrimaryKey($genusId);

        return $this->join($genusPersons, $persons, $towns, $addresses, [$genuses]);
    }

    /**
     * @param int $genusId
     *
     * @return PersonEntity[]
     */
    public function getByGenusIdCached($genusId)
    {
        return $this->cache->call([$this, 'getByGenusId'], $genusId);
    }

    /**
     * @param Fluent $query
     * @return PersonEntity[]
     */
    public function getBySubQuery(Fluent $query)
    {
        $persons = $this->personManager->getBySubQuery($query);

        $towns = $this->townFacade->getAll();
        $addresses = $this->addressFacade->getAll();
        $genuses = $this->genusManager->getAll();

        return $this->join($persons, $persons, $towns, $addresses, $genuses);
    }
}
