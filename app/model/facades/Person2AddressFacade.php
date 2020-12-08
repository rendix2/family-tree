<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddressFacade.php
 * User: Tomáš Babický
 * Date: 10.11.2020
 * Time: 2:40
 */

namespace Rendix2\FamilyTree\App\Facades;

use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Managers\Person2AddressManager;
use Rendix2\FamilyTree\App\Model\Entities\DurationEntity;
use Rendix2\FamilyTree\App\Model\Entities\Person2AddressEntity;
use Rendix2\FamilyTree\App\Model\Entities\PersonEntity;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;

/**
 * Class PersonAddressFacade
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class Person2AddressFacade
{
    /**
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var Person2AddressManager $person2AddressManager
     */
    private $person2AddressManager;

    /**
     * PersonAddressFacade constructor.
     *
     * @param AddressFacade $addressFacade
     * @param IStorage $storage
     * @param Person2AddressManager $person2AddressManager
     * @param PersonFacade $personFacade
     */
    public function __construct(
        AddressFacade $addressFacade,
        IStorage $storage,
        Person2AddressManager $person2AddressManager,
        PersonFacade $personFacade
    ) {
        $this->addressFacade = $addressFacade;
        $this->cache = new Cache($storage, self::class);
        $this->person2AddressManager = $person2AddressManager;
        $this->personFacade = $personFacade;
    }

    /**
     * @param Person2AddressEntity[] $rows
     * @param PersonEntity[] $persons
     * @param AddressFacade[] $addresses
     *
     * @return Person2AddressEntity[]
     */
    private function join(array $rows, array $persons, array $addresses)
    {
        foreach ($rows as $row) {
            foreach ($persons as $person) {
                if ($row->_personId === $person->id) {
                    $row->person = $person;
                    break;
                }
            }

            foreach ($addresses as $address) {
                if ($row->_addressId === $address->id) {
                    $row->address = $address;
                    break;
                }
            }

            $durationEntity = new DurationEntity((array)$row);
            $row->duration = $durationEntity;
        }

        return $rows;
    }

    /**
     * @return Person2AddressEntity[]
     */
    public function getAll()
    {
        $rows = $this->person2AddressManager->getAll();
        $persons = $this->personFacade->getAll();
        $addresses = $this->addressFacade->getAll();

        return $this->join($rows, $persons, $addresses);
    }

    /**
     * @return Person2AddressEntity[]
     */
    public function getAllCached()
    {
        return $this->cache->call([$this, 'getAll']);
    }

    /**
     * @param int $personId
     *
     * @return Person2AddressEntity[]
     */
    public function getByLeft($personId)
    {
        $relations = $this->person2AddressManager->getAllByLeft($personId);

        if (!$relations) {
            return [];
        }

        $persons = $this->personFacade->getAll();
        $addresses = $this->addressFacade->getAll();

        return $this->join($relations, $persons, $addresses);
    }

    /**
     * @param int $personId
     *
     * @return Person2AddressEntity[]
     */
    public function getByLeftCached($personId)
    {
        return $this->cache->call([$this, 'getByLeft'], $personId);
    }

    /**
     * @param int $addressId
     *
     * @return Person2AddressEntity[]
     */
    public function getByRight($addressId)
    {
        $relations = $this->person2AddressManager->getAllByRight($addressId);

        if (!$relations) {
            return [];
        }

        $persons = $this->personFacade->getAll();
        $addresses = $this->addressFacade->getAll();

        return $this->join($relations, $persons, $addresses);
    }

    /**
     * @param int $addressId
     *
     * @return Person2AddressEntity[]
     */
    public function getByRightCached($addressId)
    {
        return $this->cache->call([$this, 'getByRight'], $addressId);
    }

    /**
     * @param int $personId
     * @param int $addressId
     *
     * @return Person2AddressEntity
     */
    public function getByLeftAndRight($personId, $addressId)
    {
        $relation = $this->person2AddressManager->getByLeftIdAndRightId($personId, $addressId);

        if (!$relation) {
            return null;
        }

        $person = $this->personFacade->getByPrimaryKey($personId);
        $address = $this->addressFacade->getByPrimaryKey($addressId);

        return $this->join([$relation], [$person], [$address])[0];
    }

    /**
     * @param int $personId
     * @param int $addressId
     *
     * @return Person2AddressEntity
     */
    public function getByLeftAndRightCached($personId, $addressId)
    {
        return $this->cache->call([$this, 'getByLeftAndRight'], $personId, $addressId);
    }
}
