<?php
/**
 *
 * Created by PhpStorm.
 * Filename: WeddingFacadeSelector.php
 * User: Tomáš Babický
 * Date: 12.04.2021
 * Time: 2:09
 */

namespace Rendix2\FamilyTree\App\Model\Facades\Wedding;

use Dibi\Fluent;
use Nette\Caching\IStorage;
use Nette\NotImplementedException;
use Rendix2\FamilyTree\App\Filters\WeddingFilter;
use Rendix2\FamilyTree\App\Model\Entities\AddressEntity;
use Rendix2\FamilyTree\App\Model\Entities\DurationEntity;
use Rendix2\FamilyTree\App\Model\Entities\PersonEntity;
use Rendix2\FamilyTree\App\Model\Entities\TownEntity;
use Rendix2\FamilyTree\App\Model\Entities\WeddingEntity;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Model\Facades\DefaultFacade\DefaultFacadeSelector;
use Rendix2\FamilyTree\App\Model\Facades\TownFacade;
use Rendix2\FamilyTree\App\Model\Managers\PersonManager;
use Rendix2\FamilyTree\App\Model\Managers\Wedding\Interfaces\IWeddingSelector;
use Rendix2\FamilyTree\App\Model\Managers\WeddingManager;

/**
 * Class WeddingFacadeSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Facades\Wedding
 */
class WeddingFacadeSelector extends DefaultFacadeSelector implements IWeddingSelector
{
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
     * @param IStorage       $storage
     * @param AddressFacade  $addressFacade
     * @param PersonManager  $personManager
     * @param TownFacade     $townFacade
     * @param WeddingFilter  $weddingFilter
     * @param WeddingManager $weddingManager
     */
    public function __construct(
        AddressFacade $addressFacade,
        PersonManager $personManager,
        TownFacade $townFacade,
        WeddingFilter $weddingFilter,
        WeddingManager $weddingManager
    ) {
        parent::__construct($weddingFilter);

        $this->addressFacade = $addressFacade;
        $this->townFacade = $townFacade;

        $this->personManager = $personManager;
        $this->weddingManager = $weddingManager;
    }

    public function __destruct()
    {
        $this->personManager = null;
        $this->weddingManager = null;

        $this->townFacade = null;
        $this->addressFacade = null;

        parent::__destruct();
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
     * @param int $id
     *
     * @return WeddingEntity
     */
    public function getByPrimaryKey($id)
    {
        $wedding = $this->weddingManager->select()->getManager()->getByPrimaryKey($id);

        if (!$wedding) {
            return null;
        }

        $persons = $this->personManager->select()->getManager()->getByPrimaryKeys(
            [
                $wedding->_husbandId,
                $wedding->_wifeId
            ]
        );

        $town = [];

        if ($wedding->_townId) {
            $town[] = $this->townFacade->select()->getManager()->getByPrimaryKey($wedding->_townId);
        }

        $address = [];

        if ($wedding->_addressId) {
            $address[] = $this->addressFacade->select()->getManager()->getByPrimaryKey($wedding->_addressId);
        }

        return $this->join([$wedding], $persons, $town, $address)[0];
    }

    public function getByPrimaryKeys(array $ids)
    {
        throw new NotImplementedException();
    }

    public function getColumnFluent($column)
    {
        throw new NotImplementedException();
    }

    /**
     * @return WeddingEntity[]
     */
    public function getAll()
    {
        $weddings = $this->weddingManager->select()->getCachedManager()->getAll();

        $husbandIds = $this->weddingManager->select()->getManager()->getColumnFluent('husbandId');
        $wifeIds = $this->weddingManager->select()->getManager()->getColumnFluent('wifeId');

        $husbands = $this->personManager->select()->getManager()->getBySubQuery($husbandIds);
        $wives = $this->personManager->select()->getManager()->getBySubQuery($wifeIds);

        $persons = array_merge($husbands, $wives);

        $townIds = $this->getIds($weddings, '_townId');
        $addressIds = $this->getIds($weddings, '_addressId');

        $towns = $this->townFacade->select()->getManager()->getByPrimaryKeys($townIds);
        $addresses = $this->addressFacade->select()->getManager()->getByPrimaryKeys($addressIds);

        return $this->join($weddings, $persons, $towns, $addresses);
    }

    public function getAllPairs()
    {
        throw new NotImplementedException();
    }

    public function getPairs($column)
    {
        throw new NotImplementedException();
    }

    public function getBySubQuery(Fluent $query)
    {
        throw new NotImplementedException();
    }

    public function getAllByHusbandId($husbandId)
    {
        $weddings = $this->weddingManager->select()->getManager()->getAllByHusbandId($husbandId);

        if (!$weddings) {
            return [];
        }

        $persons = $this->personManager->select()->getCachedManager()->getAll();
        $towns = $this->townFacade->select()->getCachedManager()->getAll();
        $addresses = $this->addressFacade->select()->getCachedManager()->getAll();

        return $this->join($weddings, $persons, $towns, $addresses);
    }

    public function getAllByWifeId($wifeId)
    {
        $weddings = $this->weddingManager->select()->getManager()->getAllByWifeId($wifeId);

        if (!$weddings) {
            return [];
        }

        $persons = $this->personManager->select()->getCachedManager()->getAll();
        $towns = $this->townFacade->select()->getCachedManager()->getAll();
        $addresses = $this->addressFacade->select()->getCachedManager()->getAll();

        return $this->join($weddings, $persons, $towns, $addresses);
    }

    public function getByWifeIdAndHusbandId($wifeId, $husbandId)
    {
        $wedding = $this->weddingManager->select()->getManager()->getByWifeIdAndHusbandId($wifeId, $husbandId);

        if (!$wedding) {
            return null;
        }

        $persons = $this->personManager->select()->getCachedManager()->getAll();
        $towns = $this->townFacade->select()->getCachedManager()->getAll();
        $addresses = $this->addressFacade->select()->getCachedManager()->getAll();

        return $this->join([$wedding], $persons, $towns, $addresses);
    }

    public function getByTownId($townId)
    {
        $weddings = $this->weddingManager->select()->getManager()->getByTownId($townId);

        if (!$weddings) {
            return [];
        }

        $persons = $this->personManager->select()->getCachedManager()->getAll();
        $town = $this->townFacade->select()->getManager()->getByPrimaryKey($townId);
        $addresses = $this->addressFacade->select()->getCachedManager()->getAll();

        return $this->join($weddings, $persons, [$town], $addresses);
    }

    public function getByAddressId($addressId)
    {
        $weddings = $this->weddingManager->select()->getManager()->getByAddressId($addressId);

        if (!$weddings) {
            return [];
        }

        $persons = $this->personManager->select()->getCachedManager()->getAll();
        $towns = $this->townFacade->select()->getCachedManager()->getAll();
        $address = $this->addressFacade->select()->getManager()->getByPrimaryKey($addressId);

        return $this->join($weddings, $persons, $towns, [$address]);
    }
}
