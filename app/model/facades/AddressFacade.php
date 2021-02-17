<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressFacade.php
 * User: Tomáš Babický
 * Date: 10.11.2020
 * Time: 1:26
 */

namespace Rendix2\FamilyTree\App\Model\Facades;

use Dibi\Fluent;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Managers\AddressManager;
use Rendix2\FamilyTree\App\Managers\TownManager;
use Rendix2\FamilyTree\App\Model\Entities\AddressEntity;
use Rendix2\FamilyTree\App\Model\Entities\TownEntity;

/**
 * Class AddressFacade
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class AddressFacade
{
    use GetIds;

    /**
     * @var AddressManager $addressManager
     */
    private $addressManager;

    /**
     * @var AddressFilter $addressFilter
     */
    private $addressFilter;

    /**
     * @var Cache $cache
     */
    private $cache;

    /**
     * @var TownFacade $townManager
     */
    private $townFacade;

    /**
     * @var TownManager $townManager
     */
    private $townManager;

    /**
     * AddressFacade constructor.
     *
     * @param AddressManager $addressManager
     * @param TownFacade $townFacade
     * @param TownManager $townManager
     * @param IStorage $storage
     */
    public function __construct(
        AddressManager $addressManager,
        AddressFilter $addressFilter,
        TownFacade $townFacade,
        TownManager $townManager,
        IStorage $storage
    ) {
        $this->addressManager = $addressManager;

        $this->addressFilter = $addressFilter;

        $this->cache = new Cache($storage, self::class);
        $this->townFacade = $townFacade;
        $this->townManager = $townManager;
    }

    /**
     * @param AddressEntity[] $addresses
     * @param TownEntity[] $towns
     *
     * @return AddressEntity[]
     */
    public function join(array $addresses, array $towns)
    {
        foreach ($addresses as $address) {
            foreach ($towns as $town) {
                if ($town->id === $address->_townId) {
                    $address->town = $town;
                    unset($address->townId, $address->countryId);
                    break;
                }
            }

            $address->clean();
        }

        return $addresses;
    }

    /**
     * @return AddressEntity[]
     */
    public function getAll()
    {
        $addresses = $this->addressManager->getAll();

        $townIds = $this->getIds($addresses, '_townId');

        $towns = $this->townFacade->getByPrimaryKeys($townIds);

        return $this->join($addresses, $towns);
    }

    /**
     * @return AddressEntity[]
     */
    public function getAllCached()
    {
        return $this->cache->call([$this, 'getAll']);
    }

    /**
     * @return AddressEntity[]
     */
    public function getAllPairs()
    {
        $addressFilter = $this->addressFilter;

        $addresses = $this->getAll();
        $resultAddresses = [];

        foreach ($addresses as $address) {
            $resultAddresses[$address->id] = $addressFilter($address);
        }

        return $resultAddresses;
    }

    /**
     * @return AddressEntity[]
     */
    public function getPairsCached()
    {
        return $this->cache->call([$this, 'getAllPairs']);
    }

    /**
     * @param int $townId
     *
     * @return AddressEntity[]
     */
    public function getByTownPairs($townId)
    {
        $addressFilter = $this->addressFilter;

        $addresses = $this->getByTownId($townId);
        $resultAddresses = [];

        foreach ($addresses as $address) {
            $resultAddresses[$address->id] = $addressFilter($address);
        }

        return $resultAddresses;
    }

    /**
     * @param int $townId
     *
     * @return AddressEntity[]
     */
    public function getByTownPairsCached($townId)
    {
        return $this->cache->call([$this, 'getByTownPairs'], $townId);
    }

    /**
     * @param int $addressId
     *
     * @return AddressEntity
     */
    public function getByPrimaryKey($addressId)
    {
        $address = $this->addressManager->getByPrimaryKey($addressId);

        if (!$address) {
            return null;
        }

        $town = $this->townFacade->getByPrimaryKey($address->_townId);

        return $this->join([$address], [$town])[0];
    }

    /**
     * @param int $addressId
     *
     * @return AddressEntity
     */
    public function getByPrimaryKeyCached($addressId)
    {
        return $this->cache->call([$this,'getByPrimaryKey'], $addressId);
    }

    /**
     * @param array $addressIds
     *
     * @return AddressEntity[]
     */
    public function getByPrimaryKeys(array $addressIds)
    {
        $addresses = $this->addressManager->getByPrimaryKeys($addressIds);

        if (!$addresses) {
            return [];
        }

        $townIds = $this->getIds($addresses, '_townId');
        $towns = $this->townFacade->getByPrimaryKeys($townIds);

        return $this->join($addresses, $towns);
    }

    /**
     * @param int $countryId
     *
     * @return AddressEntity[]
     */
    public function getByCountryId($countryId)
    {
        $addresses = $this->addressManager->getAllByCountryId($countryId);
        $towns = $this->townFacade->getByCountryId($countryId);

        return $this->join($addresses, $towns);
    }

    /**
     * @param int $countryId
     *
     * @return AddressEntity[]
     */
    public function getByCountryIdCached($countryId)
    {
        return $this->cache->call([$this, 'getByCountryId'], $countryId);
    }

    /**
     * @param int $townId
     *
     * @return AddressEntity[]
     */
    public function getByTownId($townId)
    {
        $addresses = $this->addressManager->getByTownId($townId);
        $town = $this->townFacade->getByPrimaryKey($townId);

        return $this->join($addresses, [$town]);
    }

    /**
     * @param int $townId
     *
     * @return AddressEntity[]
     */
    public function getByTownIdCached($townId)
    {
        return $this->cache->call([$this, 'getByTownId'], $townId);
    }

    /**
     * @return AddressEntity[]
     */
    public function getToMap()
    {
        $addresses = $this->addressManager->getPairsToMap();
        $towns = $this->townFacade->getAll();

        return $this->join($addresses, $towns);
    }

    /**
     * @param Fluent $query
     *
     * @return AddressEntity[]
     */
    public function getBySubQuery(Fluent $query)
    {
        $addresses = $this->addressManager->getBySubQuery($query);

        $townIds = $this->getIds($addresses, '_townId');

        $towns = $this->townFacade->getByPrimaryKeys($townIds);

        return $this->join($addresses, $towns);
    }
}
