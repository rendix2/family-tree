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

use Dibi\Connection;
use Dibi\Fluent;
use Dibi\Row;
use Nette\NotImplementedException;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Model\Entities\AddressEntity;
use Rendix2\FamilyTree\App\Model\Entities\CountryEntity;
use Rendix2\FamilyTree\App\Model\Entities\TownEntity;
use Rendix2\FamilyTree\App\Model\Facades\DefaultFacade\DefaultFacadeSelector;
use Rendix2\FamilyTree\App\Model\Managers\Address\AddressTable;
use Rendix2\FamilyTree\App\Model\Managers\Address\Interfaces\IAddressSelector;
use Rendix2\FamilyTree\App\Model\Managers\Country\CountryTable;
use Rendix2\FamilyTree\App\Model\Managers\Tables;
use Rendix2\FamilyTree\App\Model\Managers\Town\TownTable;

/**
 * Class AddressFacadeSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Facades\Address
 */
class AddressFacadeSelector extends DefaultFacadeSelector implements IAddressSelector
{
    /**
     * @var AddressTable $addressTable
     */
    private $addressTable;

    /**
     * @var Connection $connection
     */
    private $connection;

    /**
     * @var CountryTable $countryTable
     */
    private $countryTable;

    /**
     * @var TownTable $townTable
     */
    private $townTable;

    /**
     * AddressFacadeSelector constructor.
     *
     * @param AddressTable  $addressTable
     * @param AddressFilter $addressFilter
     * @param Connection    $connection
     * @param CountryTable  $countryTable
     * @param TownTable     $townTable
     */
    public function __construct(
        AddressTable $addressTable,
        AddressFilter $addressFilter,
        Connection $connection,
        CountryTable $countryTable,
        TownTable $townTable
    ) {
        parent::__construct($addressFilter);

        $this->addressTable = $addressTable;
        $this->connection = $connection;
        $this->countryTable = $countryTable;
        $this->townTable = $townTable;
    }

    /**
     * @return Fluent
     */
    public function getAllFluent()
    {
        $query = $this->connection;

        $columns = $this->addressTable->getColumns();

        foreach ($columns as $column) {
            $query = $query->select('a.' . $column)
                ->as('a.' . $column);
        }

        $columns = $this->townTable->getColumns();

        foreach ($columns as $column) {
            $query = $query->select('t.' . $column)
                ->as('t.' . $column);
        }

        $columns = $this->countryTable->getColumns();

        foreach ($columns as $column) {
            $query = $query->select('c.' . $column)
                ->as('c.' . $column);
        }

        return $query->from(Tables::ADDRESS_TABLE)
            ->as('a')
            ->innerJoin(Tables::TOWN_TABLE)
            ->as('t')
            ->on('[a.townId] = [t.id]')
            ->innerJoin(Tables::COUNTRY_TABLE)
            ->as('c')
            ->on('[a.countryId] = [c.id]');
    }

    /**
     * @param Row[] $rows
     *
     * @return AddressEntity[]
     */
    public function join(array $rows)
    {
        $addresses = [];

        foreach ($rows as $row) {
            $addressEntity = new AddressEntity([]);
            $townEntity = new TownEntity([]);
            $countryEntity = new CountryEntity([]);

            foreach ($row as $column => $value) {
                if (strpos($column, 'a.') === 0) {
                    $addressColumn = substr($column, 2);
                    $addressEntity->{$addressColumn} = $value;
                }

                if (strpos($column, 't.') === 0) {
                    $townColumn = substr($column, 2);
                    $townEntity->{$townColumn} = $value;
                }

                if (strpos($column, 'c.') === 0) {
                    $countryColumn = substr($column, 2);
                    $countryEntity->{$countryColumn} = $value;
                }
            }

            $townEntity->country = $countryEntity;
            $addresses[$addressEntity->id] = $addressEntity;

            $addressEntity->town = $townEntity;
        }

        return array_values($addresses);
    }

    /**
     * @param int $countryId
     *
     * @return AddressEntity[]
     */
    public function getByCountryId($countryId)
    {
        $rows = $this->getAllFluent()
            ->where('a.countryId = %i', $countryId)
            ->fetchAll();

        return $this->join($rows);
    }

    /**
     * @param int $townId
     *
     * @return AddressEntity[]
     */
    public function getByTownId($townId)
    {
        $rows = $this->getAllFluent()
            ->where('a.townId = %i', $townId)
            ->fetchAll();

        return $this->join($rows);
    }

    /**
     * @return AddressEntity[]
     */
    public function getToMap()
    {
        $rows = $this->getAllFluent()
            ->where('[a.gps] IS NOT NULL')
            ->fetchAll();

        return $this->join($rows);
    }

    /**
     * @param int $id
     *
     * @return AddressEntity
     */
    public function getByPrimaryKey($id)
    {
        $rows = $this->getAllFluent()
            ->where('a.id = %i', $id)
            ->fetch();

        return $this->join([$rows])[0];
    }

    /**
     * @param array $ids
     *
     * @return array
     */
    public function getByPrimaryKeys(array $ids)
    {
        if ($this->isOnlyNull($ids)) {
            return [];
        }

        $ids = $this->uniqueIds($ids);

        $rows = $this->getAllFluent()
            ->where('a.id IN %in', $ids)
            ->fetchAll();

        return $this->join($rows);
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
        $rows = $this->getAllFluent()->fetchAll();

        return $this->join($rows);
    }

    public function getPairs($column)
    {
        throw new NotImplementedException();
    }

    /**
     * @param Fluent $query
     *
     * @return AddressEntity[]
     */
    public function getBySubQuery(Fluent $query)
    {
        $rows = $this->getAllFluent()
            ->where('[a.id] in %sql', $query)
            ->fetchAll();

        return $this->join($rows);
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
