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
use Rendix2\FamilyTree\App\Managers\AddressManager;
use Rendix2\FamilyTree\App\Managers\Person2AddressManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Model\Entities\AddressEntity;
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
     * @var AddressManager $addressManager
     */
    private $addressManager;

    /**
     * @var Cache $cache
     */
    private $cache;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var Person2AddressManager $person2AddressManager
     */
    private $person2AddressManager;

    /**
     * PersonAddressFacade constructor.
     *
     * @param AddressFacade $addressFacade
     * @param AddressManager $addressManager
     * @param IStorage $storage
     * @param Person2AddressManager $person2AddressManager
     * @param PersonFacade $personFacade
     * @param PersonManager $personManager
     */
    public function __construct(
        AddressFacade $addressFacade,
        AddressManager $addressManager,
        IStorage $storage,
        Person2AddressManager $person2AddressManager,
        PersonFacade $personFacade,
        PersonManager $personManager
    ) {
        $this->addressFacade = $addressFacade;
        $this->addressManager = $addressManager;
        $this->cache = new Cache($storage, self::class);
        $this->person2AddressManager = $person2AddressManager;
        $this->personFacade = $personFacade;
        $this->personManager = $personManager;
    }

    /**
     * @param Person2AddressEntity[] $rows
     * @param PersonEntity[] $persons
     * @param AddressEntity[] $addresses
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

            $durationEntity = new DurationEntity((array) $row);
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

        $personIds = $this->person2AddressManager->getColumnFluent('personId');
        $addressIds = $this->person2AddressManager->getColumnFluent('addressId');

        $persons = $this->personFacade->getBySubQuery($personIds);
        $addresses = $this->addressFacade->getBySubQuery($addressIds);

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

        $person = $this->personFacade->getByPrimaryKey($personId);
        $addresses = $this->addressFacade->getAll();

        return $this->join($relations, [$person], $addresses);
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

        $personIds = $this->person2AddressManager->getColumnFluent('personId');

        $persons = $this->personFacade->getBySubQuery($personIds);
        $address = $this->addressFacade->getByPrimaryKey($addressId);

        return $this->join($relations, $persons, [$address]);
    }

    /**
     * @param int $addressId
     *
     * @return Person2AddressEntity[]
     */
    public function getByRightManager($addressId)
    {
        $relations = $this->person2AddressManager->getAllByRight($addressId);

        if (!$relations) {
            return [];
        }

        $personIds = $this->person2AddressManager->getColumnFluent('personId');

        $persons = $this->personManager->getBySubQuery($personIds);
        $address = $this->addressManager->getByPrimaryKey($addressId);

        return $this->join($relations, $persons, [$address]);
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

    /**
     * @param int $addressId
     *
     * @return Person2AddressEntity[]
     */
    public function getByRightManagerCached($addressId)
    {
        return $this->cache->call([$this, 'getByRightManager'], $addressId);
    }
}
