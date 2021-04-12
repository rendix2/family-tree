<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressSelector.php
 * User: Tomáš Babický
 * Date: 03.04.2021
 * Time: 23:41
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Address;

use Dibi\Connection;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultSelector;
use Rendix2\FamilyTree\App\Model\Entities\AddressEntity;
use Rendix2\FamilyTree\App\Model\Managers\Address\Interfaces\IAddressSelector;

/**
 * Class AddressSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Managers
 */
class AddressSelector extends DefaultSelector implements IAddressSelector
{
    /**
     * AddressSelector constructor.
     *
     * @param Connection    $connection
     * @param AddressTable  $table
     * @param AddressFilter $addressFilter
     */
    public function __construct(
        Connection $connection,
        AddressTable $table,
        AddressFilter $addressFilter
    ) {
        parent::__construct($connection, $table, $addressFilter);
    }

    /**
     * @param int $countryId
     *
     * @return AddressEntity[]
     */
    public function getByCountryId($countryId)
    {
        return $this->getAllFluent()
            ->where('[countryId] = %i', $countryId)
            ->execute()
            ->setRowClass(AddressEntity::class)
            ->fetchAll();
    }

    /**
     * @param int $townId
     *
     * @return AddressEntity[]
     */
    public function getByTownId($townId)
    {
        return $this->getAllFluent()
            ->where('[townId] = %i', $townId)
            ->execute()
            ->setRowClass(AddressEntity::class)
            ->fetchAll();
    }

    /**
     * @return AddressEntity[]
     */
    public function getToMap()
    {
        return $this->getAllFluent()
            ->where('[gps] IS NOT NULL')
            ->execute()
            ->setRowClass(AddressEntity::class)
            ->fetchAll();
    }

    public function getByTownPairs($townId)
    {
        $addresses = $this->getByTownId($townId);

        return $this->applyFilter($addresses);
    }
}
