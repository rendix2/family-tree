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

use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\Localization\ITranslator;
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
     * @param PersonEntity[] $innerPersons
     * @param TownEntity[] $towns
     * @param AddressEntity[] $addresses
     * @param GenusEntity[] $genuses
     *
     * @return PersonEntity[]
     */
    private function join(array $persons, array $innerPersons, array $towns, array $addresses, array $genuses)
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

            foreach ($innerPersons as $innerPerson) {
                if ($person->_motherId === $innerPerson->id) {
                    $person->mother = $innerPerson;
                    continue;
                }

                if ($person->_fatherId === $innerPerson->id) {
                    $person->father = $innerPerson;
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
        $persons = $this->personManager->getAll();
        $towns = $this->townFacade->getAll();
        $addresses = $this->addressFacade->getAll();
        $genuses = $this->genusManager->getAll();

        return $this->join([$person], $persons, $towns, $addresses, $genuses)[0];
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
}
