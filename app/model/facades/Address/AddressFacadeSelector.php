<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddresssSelector.php
 * User: Tomáš Babický
 * Date: 05.04.2021
 * Time: 17:11
 */

namespace Rendix2\FamilyTree\App\Model\Facades\Address;

use Dibi\Fluent;
use Nette\NotImplementedException;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Model\Entities\AddressEntity;
use Rendix2\FamilyTree\App\Model\Entities\TownEntity;
use Rendix2\FamilyTree\App\Model\Facades\DefaultFacade\DefaultFacadeSelector;
use Rendix2\FamilyTree\App\Model\Facades\TownFacade;
use Rendix2\FamilyTree\App\Model\Managers\Address\Interfaces\IAddressSelector;
use Rendix2\FamilyTree\App\Model\Managers\AddressManager;

class AddressFacadeSelector extends DefaultFacadeSelector implements IAddressSelector
{
    /**
     * @var AddressManager $addressManager
     */
    private $addressManager;

    /**
     * @var TownFacade $townManager
     */
    private $townFacade;

    /**
     * AddressFacadeSelector constructor.
     *
     * @param AddressManager $addressManager
     * @param AddressFilter  $addressFilter
     * @param TownFacade     $townFacade
     */
    public function __construct(
        AddressManager $addressManager,
        AddressFilter $addressFilter,
        TownFacade $townFacade

    ) {
        parent::__construct($addressFilter);

        $this->addressManager = $addressManager;
        $this->townFacade = $townFacade;
    }

    /**
     * @param AddressEntity[] $addresses
     * @param TownEntity[] $towns
     *
     * @return AddressEntity[]
     */
    private function join(array $addresses, array $towns)
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

    public function getByCountryId($countryId)
    {
        $addresses = $this->addressManager->select()->getManager()->getByCountryId($countryId);
        $towns = $this->townFacade->select()->getManager()->getAllByCountry($countryId);

        return $this->join($addresses, $towns);
    }

    public function getByTownId($townId)
    {
        $addresses = $this->addressManager->select()->getManager()->getByTownId($townId);
        $town = $this->townFacade->select()->getManager()->getByPrimaryKey($townId);

        return $this->join($addresses, [$town]);
    }

    public function getToMap()
    {
        $addresses = $this->addressManager->select()->getManager()->getToMap();
        $towns = $this->townFacade->select()->getCachedManager()->getAll();

        return $this->join($addresses, $towns);
    }

    public function getByPrimaryKey($id)
    {
        $address = $this->addressManager->select()->getManager()->getByPrimaryKey($id);

        if (!$address) {
            return null;
        }

        $town = $this->townFacade->select()->getManager()->getByPrimaryKey($address->_townId);

        return $this->join([$address], [$town])[0];
    }

    public function getByPrimaryKeys(array $ids)
    {
        $addresses = $this->addressManager->select()->getManager()->getByPrimaryKeys($ids);

        if (!$addresses) {
            return [];
        }

        $townIds = $this->getIds($addresses, '_townId');
        $towns = $this->townFacade->select()->getManager()->getByPrimaryKeys($townIds);

        return $this->join($addresses, $towns);
    }

    public function getColumnFluent($column)
    {
        throw new NotImplementedException();
    }

    /**
     * @return AddressEntity[]
     */
    public function getAll()
    {
        $addresses = $this->addressManager->select()->getCachedManager()->getAll();

        $townIds = $this->getIds($addresses, '_townId');

        $towns = $this->townFacade->select()->getManager()->getByPrimaryKeys($townIds);

        return $this->join($addresses, $towns);
    }

    public function getPairs($column)
    {
        throw new NotImplementedException();
    }

    public function getBySubQuery(Fluent $query)
    {
        $addresses = $this->addressManager->select()->getManager()->getBySubQuery($query);

        $townIds = $this->getIds($addresses, '_townId');

        $towns = $this->townFacade->select()->getManager()->getByPrimaryKeys($townIds);

        return $this->join($addresses, $towns);
    }

    public function getByTownPairs($townId)
    {
        $addresses = $this->getByTownId($townId);

        return $this->applyFilter($addresses);
    }

    public function getAllPairs()
    {
        $rows = $this->getAll();

        return $this->applyFilter($rows);
    }
}
