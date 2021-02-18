<?php
/**
 *
 * Created by PhpStorm.
 * Filename: WeddigFacade.php
 * User: Tomáš Babický
 * Date: 10.11.2020
 * Time: 2:44
 */

namespace Rendix2\FamilyTree\App\Facades;

use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\WeddingManager;
use Rendix2\FamilyTree\App\Model\Entities\AddressEntity;
use Rendix2\FamilyTree\App\Model\Entities\DurationEntity;
use Rendix2\FamilyTree\App\Model\Entities\PersonEntity;
use Rendix2\FamilyTree\App\Model\Entities\TownEntity;
use Rendix2\FamilyTree\App\Model\Entities\WeddingEntity;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Model\Facades\GetIds;
use Rendix2\FamilyTree\App\Model\Facades\TownFacade;

/**
 * Class WeddingFacade
 *
 * @package Rendix2\FamilyTree\App\Facades
 */
class WeddingFacade
{
    use GetIds;

    /**
     * @var Cache $cache
     */
    private $cache;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var WeddingManager $weddingManager
     */
    private $weddingManager;

    /**
     * @var TownFacade $townFacade
     */
    private $townFacade;

    /**
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * WeddingFacade constructor.
     *
     * @param IStorage $storage
     * @param AddressFacade $addressFacade
     * @param PersonManager $personManager
     * @param TownFacade $townFacade
     * @param WeddingManager $weddingManager
     */
    public function __construct(
        IStorage $storage,
        AddressFacade $addressFacade,
        PersonManager $personManager,
        TownFacade $townFacade,
        WeddingManager $weddingManager
    )
    {
        $this->addressFacade = $addressFacade;
        $this->townFacade = $townFacade;

        $this->cache = new Cache($storage, self::class);

        $this->personManager = $personManager;
        $this->weddingManager = $weddingManager;
    }

    /**
     * @param WeddingEntity[] $weddings
     * @param PersonEntity[] $persons
     * @param TownEntity[] $towns
     * @param AddressEntity[] $addresses
     *
     * @return WeddingEntity[]
     */
    public function join(array $weddings, array $persons, array $towns, array $addresses)
    {
        foreach ($weddings as $wedding) {
            foreach ($persons as $person) {
                if ($wedding->_husbandId === $person->id) {
                    $wedding->husband = $person;
                    break;
                }
            }

            foreach ($persons as $person) {
                if ($wedding->_wifeId === $person->id) {
                    $wedding->wife = $person;
                    break;
                }
            }

            foreach ($towns as $town) {
                if ($wedding->_townId === $town->id) {
                    $wedding->town = $town;
                    break;
                }
            }

            foreach ($addresses as $address) {
                if ($wedding->_addressId === $address->id) {
                    $wedding->address = $address;
                    break;
                }
            }

            $durationEntity = new DurationEntity((array) $wedding);
            $wedding->duration = $durationEntity;

            $wedding->clean();
        }

        return $weddings;
    }

    /**
     * @return WeddingEntity[]
     */
    public function getAll()
    {
        $weddings = $this->weddingManager->getAll();

        $husbandIds = $this->weddingManager->getColumnFluent('husbandId');
        $wifeIds = $this->weddingManager->getColumnFluent('wifeId');

        $husbands = $this->personManager->getBySubQuery($husbandIds);
        $wives = $this->personManager->getBySubQuery($wifeIds);

        $persons = array_merge($husbands, $wives);

        $townIds = $this->getIds($weddings, '_townId');
        $addressIds = $this->getIds($weddings, '_addressId');

        $towns = $this->townFacade->getByPrimaryKeys($townIds);
        $addresses = $this->addressFacade->getByPrimaryKeys($addressIds);

        return $this->join($weddings, $persons, $towns, $addresses);
    }

    /**
     * @return WeddingEntity[]
     */
    public function getAllCached()
    {
        return $this->cache->call([$this, 'getAll']);
    }

    /**
     * @param int $weddingId
     *
     * @return WeddingEntity
     */
    public function getByPrimaryKey($weddingId)
    {
        $wedding = $this->weddingManager->getByPrimaryKey($weddingId);

        if (!$wedding) {
            return null;
        }

        $persons = $this->personManager->getByPrimaryKeys(
            [
                $wedding->_husbandId,
                $wedding->_wifeId
            ]
        );

        $town = [];

        if ($wedding->_townId) {
            $town[] = $this->townFacade->getByPrimaryKey($wedding->_townId);
        }

        $address = [];

        if ($wedding->_addressId) {
            $address[] = $this->addressFacade->getByPrimaryKey($wedding->_addressId);
        }

        return $this->join([$wedding], $persons, $town, $address)[0];
    }

    /**
     * @param int $weddingId
     *
     * @return WeddingEntity
     */
    public function getByPrimaryKeyCached($weddingId)
    {
        return $this->cache->call([$this, 'getByPrimaryKey'], $weddingId);
    }

    /**
     * @param int $wifeId
     *
     * @return WeddingEntity[]
     */
    public function getByWifeId($wifeId)
    {
        $weddings = $this->weddingManager->getAllByWifeId($wifeId);

        if (!$weddings) {
            return [];
        }

        $persons = $this->personManager->getAll();
        $towns = $this->townFacade->getAll();
        $addresses = $this->addressFacade->getAll();

        return $this->join($weddings, $persons, $towns, $addresses);
    }

    /**
     * @param int $wifeId
     *
     * @return WeddingEntity[]
     */
    public function getByWifeIdCached($wifeId)
    {
        return $this->cache->call([$this, 'getByWifeId'], $wifeId);
    }

    /**
     * @param int $husbandId
     *
     * @return WeddingEntity[]
     */
    public function getByHusbandId($husbandId)
    {
        $weddings = $this->weddingManager->getAllByHusbandId($husbandId);

        if (!$weddings) {
            return [];
        }

        $persons = $this->personManager->getAll();
        $towns = $this->townFacade->getAll();
        $addresses = $this->addressFacade->getAll();

        return $this->join($weddings, $persons, $towns, $addresses);
    }

    /**
     * @param int $husbandId
     *
     * @return WeddingEntity[]
     */
    public function getByHusbandIdCached($husbandId)
    {
        return $this->cache->call([$this, 'getByHusbandId'], $husbandId);
    }

    /**
     * @param int $townId
     *
     * @return WeddingEntity[]
     */
    public function getByTownId($townId)
    {
        $weddings = $this->weddingManager->getByTownId($townId);

        if (!$weddings) {
            return [];
        }

        $persons = $this->personManager->getAll();
        $town = $this->townFacade->getByPrimaryKey($townId);
        $addresses = $this->addressFacade->getAll();

        return $this->join($weddings, $persons, [$town], $addresses);
    }

    /**
     * @param int $townId
     *
     * @return WeddingEntity[]
     */
    public function getByTownIdCached($townId)
    {
        return $this->cache->call([$this, 'getByTownId'], $townId);
    }

    /**
     * @param int $addressId
     *
     * @return WeddingEntity[]
     */
    public function getByAddressId($addressId)
    {
        $weddings = $this->weddingManager->getByAddressId($addressId);

        if (!$weddings) {
            return [];
        }

        $persons = $this->personManager->getAll();
        $towns = $this->townFacade->getAll();
        $address = $this->addressFacade->getByPrimaryKey($addressId);

        return $this->join($weddings, $persons, $towns, [$address]);
    }

    /**
     * @param int $addressId
     *
     * @return WeddingEntity[]
     */
    public function getByAddressIdCached($addressId)
    {
        return $this->cache->call([$this, 'getByAddressId'], $addressId);
    }
}
