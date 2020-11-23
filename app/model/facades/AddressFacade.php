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

use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Managers\AddressManager;
use Rendix2\FamilyTree\App\Model\Entities\AddressEntity;
use Rendix2\FamilyTree\App\Model\Entities\TownEntity;

/**
 * Class AddressFacade
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class AddressFacade
{
    private static $addresses;

    /**
     * @var AddressManager $addressManager
     */
    private $addressManager;

    /**
     * @var TownFacade $townManager
     */
    private $townFacade;

    /**
     * @var Cache $cache
     */
    private $cache;

    /**
     * AddressFacade constructor.
     *
     * @param AddressManager $addressManager
     * @param TownFacade $townFacade
     * @param IStorage $storage
     */
    public function __construct(
        AddressManager $addressManager,
        TownFacade $townFacade,
        IStorage $storage
    ) {
        $this->addressManager = $addressManager;
        $this->cache = new Cache($storage, self::class);
        $this->townFacade = $townFacade;
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

            $address->clean();;
        }

        return $addresses;
    }

    /**
     * @return AddressEntity[]
     */
    public function getAll()
    {
        $addresses = $this->addressManager->getAll();
        $towns = $this->townFacade->getAll();

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
    public function getPairs()
    {
        $addressFilter = new AddressFilter();

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
        return $this->cache->call([$this, 'getPairs']);
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
     * @param int $countryId
     *
     * @return AddressEntity[]
     */
    public function getByCountryId($countryId)
    {
        $addresses = $this->addressManager->getAllByCountryId($countryId);
        $towns = $this->townFacade->getAll();

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
        $towns = $this->townFacade->getAll();

        return $this->join($addresses, $towns);
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
}
