<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownFacadeSelector.php
 * User: Tomáš Babický
 * Date: 10.04.2021
 * Time: 14:48
 */

namespace Rendix2\FamilyTree\App\Model\Facades\Town;

use Dibi\Connection;
use Dibi\Fluent;
use Nette\NotImplementedException;
use Rendix2\FamilyTree\App\Filters\TownFilter;
use Rendix2\FamilyTree\App\Model\Entities\CountryEntity;
use Rendix2\FamilyTree\App\Model\Entities\TownEntity;
use Rendix2\FamilyTree\App\Model\Facades\DefaultFacade\DefaultFacadeSelector;
use Rendix2\FamilyTree\App\Model\Managers\Country\CountryTable;
use Rendix2\FamilyTree\App\Model\Managers\Tables;
use Rendix2\FamilyTree\App\Model\Managers\Town\Interfaces\ITownSelector;
use Rendix2\FamilyTree\App\Model\Managers\Town\TownTable;

/**
 * Class TownFacadeSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Facades\Town
 */
class TownFacadeSelector extends DefaultFacadeSelector implements ITownSelector
{
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
     * TownFacade constructor.
     *
     * @param Connection   $connection
     * @param CountryTable $countryTable
     * @param TownFilter   $townFilter
     * @param TownTable    $townTable
     */
    public function __construct(
        Connection $connection,
        CountryTable $countryTable,
        TownFilter $townFilter,
        TownTable $townTable
    ) {
        parent::__construct($townFilter);

        $this->connection = $connection;
        $this->countryTable = $countryTable;
        $this->townTable = $townTable;
    }

    public function getTownTable()
    {
        return $this->townTable;
    }

    public function getCountryTable()
    {
        return $this->countryTable;
    }

    public function getAllFluent()
    {
        $query = $this->connection;

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

        return $query->from(Tables::TOWN_TABLE)
            ->as('t')
            ->innerJoin(Tables::COUNTRY_TABLE)
            ->as('c')
            ->on('[t.countryId] = [c.id]');
    }

    protected function join(array $rows)
    {
        $towns = [];

        foreach ($rows as $row) {
            $townEntity = new TownEntity([]);
            $countryEntity = new CountryEntity([]);

            foreach ($row as $column => $value) {
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
            $towns[$townEntity->id] = $townEntity;
        }

        return array_values($towns);
    }

    /**
     * @param int $id
     *
     * @return TownEntity
     */
    public function getByPrimaryKey($id)
    {
        $row = $this->getAllFluent()
            ->where('[t.id] = %i', $id)
            ->fetch();

        return $this->join([$row])[0];
    }

    /**
     * @param array $ids
     *
     * @return TownEntity[]
     */
    public function getByPrimaryKeys(array $ids)
    {
        $rows = $this->getAllFluent()
            ->where('[t.id] IN %in', $ids)
            ->fetchAll();

        return $this->join($rows);
    }

    public function getColumnFluent($column)
    {
        throw new NotImplementedException();
    }

    /**
     * @return TownEntity[]
     */
    public function getAll()
    {
        $rows = $this->getAllFluent()
            ->fetchAll();

        return $this->join($rows);
    }

    public function getPairs($column)
    {
        throw new NotImplementedException();
    }

    public function getBySubQuery(Fluent $query)
    {
        throw new NotImplementedException();
    }

    public function getPairsByCountry($countryId)
    {
        throw new NotImplementedException();
    }

    public function getAllPairs()
    {
        throw new NotImplementedException();
    }

    /**
     * @param int $countryId
     *
     * @return TownEntity[]
     */
    public function getAllByCountry($countryId)
    {
        $rows = $this->getAllFluent()
            ->where('[t.countryId] = %i', $countryId)
            ->fetchAll();

        return $this->join($rows);
    }

    public function getToMap()
    {
        $rows = $this->getAllFluent()
            ->where('[t.gps] IS NOT NULL')
            ->fetchAll();

        return $this->join($rows);
    }
}
