<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonFacadeSelector.php
 * User: Tomáš Babický
 * Date: 10.04.2021
 * Time: 2:43
 */

namespace Rendix2\FamilyTree\App\Model\Facades\Person;

use Dibi\Connection;
use Dibi\Fluent;
use Dibi\Row;
use Nette\NotImplementedException;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Model\Entities\AddressEntity;
use Rendix2\FamilyTree\App\Model\Entities\CountryEntity;
use Rendix2\FamilyTree\App\Model\Entities\PersonEntity;
use Rendix2\FamilyTree\App\Model\Entities\TownEntity;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Model\Facades\DefaultFacade\DefaultFacadeSelector;
use Rendix2\FamilyTree\App\Model\Facades\TownFacade;
use Rendix2\FamilyTree\App\Model\Managers\Address\AddressTable;
use Rendix2\FamilyTree\App\Model\Managers\Country\CountryTable;
use Rendix2\FamilyTree\App\Model\Managers\Genus\GenusTable;
use Rendix2\FamilyTree\App\Model\Managers\GenusManager;
use Rendix2\FamilyTree\App\Model\Managers\Person\Interfaces\IPersonSelector;
use Rendix2\FamilyTree\App\Model\Managers\Person\PersonTable;
use Rendix2\FamilyTree\App\Model\Managers\PersonManager;
use Rendix2\FamilyTree\App\Model\Managers\Tables;
use Rendix2\FamilyTree\App\Model\Managers\Town\TownTable;

/**
 * Class PersonFacadeSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Facades\Person
 */
class PersonFacadeSelector extends DefaultFacadeSelector implements IPersonSelector
{
    /**
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;
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
     * @var GenusManager $genusManager
     */
    private $genusManager;
    /**
     * @var GenusTable
     */
    private $genusTable;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var PersonTable $personTable
     */
    private $personTable;

    /**
     * @var TownFacade $townFacade
     */
    private $townFacade;
    /**
     * @var TownTable $townTable
     */
    private $townTable;

    /**
     * PersonFacade constructor.
     *
     * @param AddressFacade $addressFacade
     * @param GenusManager  $genusManager
     * @param PersonFilter  $personFilter
     * @param PersonManager $personManager
     * @param TownFacade    $townFacade
     */
    public function __construct(
        AddressTable $addressTable,
        AddressFacade $addressFacade,
        Connection $connection,
        CountryTable $countryTable,
        GenusTable $genusTable,
        GenusManager $genusManager,
        PersonFilter  $personFilter,
        PersonManager $personManager,
        PersonTable $personTable,
        TownTable $townTable,
        TownFacade $townFacade
    ) {
        parent::__construct($personFilter);

        $this->addressTable = $addressTable;
        $this->addressFacade = $addressFacade;
        $this->connection = $connection;
        $this->countryTable = $countryTable;
        $this->genusManager = $genusManager;

        $this->genusTable = $genusTable;
        $this->personManager = $personManager;
        $this->personTable = $personTable;
        $this->townFacade = $townFacade;
        $this->townTable= $townTable;
    }

    /**
     * @return AddressFacade
     */
    public function getAddressFacade()
    {
        return $this->addressFacade;
    }

    /**
     * @return GenusManager
     */
    public function getGenusManager()
    {
        return $this->genusManager;
    }

    /**
     * @return PersonManager
     */
    public function getPersonManager()
    {
        return $this->personManager;
    }

    /**
     * @return PersonTable
     */
    public function getPersonTable()
    {
        return $this->personTable;
    }

    /**
     * @return TownFacade
     */
    public function getTownFacade()
    {
        return $this->townFacade;
    }

    /**
     * @return FLuent
     */
    public function getAllFluent()
    {
        $query = $this->connection;

        $columns = $this->personTable->getColumns();

        foreach ($columns as $column) {
            $query = $query->select('p.' . $column)
                ->as('p.' . $column);
        }

        $columns = $this->genusTable->getColumns();

        foreach ($columns as $column) {
            $query = $query->select('g.' . $column)
                ->as('g.' . $column);
        }

        $columns = $this->personTable->getColumns();

        foreach ($columns as $column) {
            $query = $query->select('mother.' . $column)
                ->as('mother.' . $column);
        }

        $columns = $this->personTable->getColumns();

        foreach ($columns as $column) {
            $query = $query->select('father.' . $column)
                ->as('father.' . $column);
        }

        // birth

        $columns = $this->countryTable->getColumns();

        foreach ($columns as $column) {
            $query = $query->select('birthCountry.' . $column)
                ->as('birthCountry.' . $column);
        }

        $columns = $this->townTable->getColumns();

        foreach ($columns as $column) {
            $query = $query->select('birthTown.' . $column)
                ->as('birthTown.' . $column);
        }

        $columns = $this->addressTable->getColumns();

        foreach ($columns as $column) {
            $query = $query->select('birthAddress.' . $column)
                ->as('birthAddress.' . $column);
        }

        // death

        $columns = $this->countryTable->getColumns();

        foreach ($columns as $column) {
            $query = $query->select('deathCountry.' . $column)
                ->as('deathCountry.' . $column);
        }

        $columns = $this->townTable->getColumns();

        foreach ($columns as $column) {
            $query = $query->select('deathTown.' . $column)
                ->as('deathTown.' . $column);
        }

        $columns = $this->addressTable->getColumns();

        foreach ($columns as $column) {
            $query = $query->select('deathAddress.' . $column)
                ->as('deathAddress.' . $column);
        }

        // graved

        $columns = $this->countryTable->getColumns();

        foreach ($columns as $column) {
            $query = $query->select('gravedCountry.' . $column)
                ->as('gravedCountry.' . $column);
        }

        $columns = $this->townTable->getColumns();

        foreach ($columns as $column) {
            $query = $query->select('gravedTown.' . $column)
                ->as('gravedTown.' . $column);
        }

        $columns = $this->addressTable->getColumns();

        foreach ($columns as $column) {
            $query = $query->select('gravedAddress.' . $column)
                ->as('gravedAddress.' . $column);
        }

        return $query->from(Tables::PERSON_TABLE)
            ->as('p')

            ->leftJoin(Tables::GENUS_TABLE)
            ->as('g')
            ->on('[p.genusId] = [g.id]')

            ->leftJoin(Tables::PERSON_TABLE)
            ->as('mother')
            ->on('[p.motherId] = [mother.id]')

            ->leftJoin(Tables::PERSON_TABLE)
            ->as('father')
            ->on('[p.fatherId] = [father.id]')

            ->leftJoin(Tables::ADDRESS_TABLE)
            ->as('birthAddress')
            ->on('[p.birthAddressId] = [birthAddress.id]')

            ->leftJoin(Tables::ADDRESS_TABLE)
            ->as('deathAddress')
            ->on('[p.deathAddressId] = [deathAddress.id]')

            ->leftJoin(Tables::ADDRESS_TABLE)
            ->as('gravedAddress')
            ->on('[p.gravedAddressId] = [gravedAddress.id]')

            ->leftJoin(Tables::TOWN_TABLE)
            ->as('birthTown')
            ->on('[p.birthTownId] = [birthTown.id]')

            ->leftJoin(Tables::TOWN_TABLE)
            ->as('deathTown')
            ->on('[p.deathTownId] = [deathTown.id]')

            ->leftJoin(Tables::TOWN_TABLE)
            ->as('gravedTown')
            ->on('[p.gravedTownId] = [gravedTown.id]')

            ->leftJoin(Tables::COUNTRY_TABLE)
            ->as('birthCountry')
            ->on('[birthTown.countryId] = [birthCountry.id]')

            ->leftJoin(Tables::COUNTRY_TABLE)
            ->as('deathCountry')
            ->on('[deathTown.countryId] = [deathCountry.id]')

            ->leftJoin(Tables::COUNTRY_TABLE)
            ->as('gravedCountry')
            ->on('[gravedTown.countryId] = [gravedCountry.id]');
    }

    /**
     * @param Row[] $rows
     *
     * @return PersonEntity[]
     */
    public function join(array $rows)
    {
        $persons = [];

        foreach ($rows as $row) {
            $personEntity = new PersonEntity([]);
            $motherEntity = new PersonEntity([]);
            $fatherEntity = new PersonEntity([]);

            $birthTownEntity = new TownEntity([]);
            $deathTownEntity = new TownEntity([]);
            $gravedTownEntity = new TownEntity([]);

            $birthAddressEntity = new AddressEntity([]);
            $deathAddressEntity = new AddressEntity([]);
            $gravedAddressEntity = new AddressEntity([]);

            $birthCountryEntity = new CountryEntity([]);
            $deathCountryEntity = new CountryEntity([]);
            $gravedCountryEntity = new CountryEntity([]);

            foreach ($row as $column => $value) {
                if (strpos($column, 'p.') === 0) {
                    $personColumn = substr($column, 2);
                    $personEntity->{$personColumn} = $value;
                }

                if (strpos($column, 'mother.') === 0) {
                    $motherColumn = substr($column, 7);
                    $motherEntity->{$motherColumn} = $value;
                }

                if (strpos($column, 'father.') === 0) {
                    $fatherColumn = substr($column, 7);
                    $fatherEntity->{$fatherColumn} = $value;
                }

                if (strpos($column, 'birthCountry.') === 0) {
                    $birthCountryColumn = substr($column, 13);
                    $birthCountryEntity->{$birthCountryColumn} = $value;
                }

                if (strpos($column, 'birthTown.') === 0) {
                    $birthTownColumn = substr($column, 10);
                    $birthTownEntity->{$birthTownColumn} = $value;
                }

                if (strpos($column, 'birthAddress.') === 0) {
                    $birthAddressColumn = substr($column, 13);
                    $birthAddressEntity->{$birthAddressColumn} = $value;
                }

                if (strpos($column, 'deathCountry.') === 0) {
                    $deathCountryColumn = substr($column, 13);
                    $deathCountryEntity->{$deathCountryColumn} = $value;
                }

                if (strpos($column, 'deathTown.') === 0) {
                    $deathTownColumn = substr($column, 10);
                    $deathTownEntity->{$deathTownColumn} = $value;
                }

                if (strpos($column, 'deathAddress.') === 0) {
                    $deathAddressColumn = substr($column, 13);
                    $deathAddressEntity->{$deathAddressColumn} = $value;
                }

                if (strpos($column, 'gravedCountry.') === 0) {
                    $gravedCountryColumn = substr($column, 14);
                    $gravedCountryEntity->{$gravedCountryColumn} = $value;
                }

                if (strpos($column, 'gravedTown.') === 0) {
                    $gravedTownColumn = substr($column, 11);
                    $gravedTownEntity->{$gravedTownColumn} = $value;
                }

                if (strpos($column, 'gravedAddress.') === 0) {
                    $gravedAddressColumn = substr($column, 14);
                    $gravedAddressEntity->{$gravedAddressColumn} = $value;
                }
            }

            if ($personEntity->motherId) {
                $personEntity->mother = $motherEntity;
            }

            if ($personEntity->fatherId) {
                $personEntity->father = $fatherEntity;
            }

            if ($personEntity->birthTownId) {
                $birthTownEntity->country = $birthCountryEntity;
                $personEntity->birthTown = $birthTownEntity;
            }

            if ($personEntity->birthAddressId) {
                $birthAddressEntity->town = $birthTownEntity;
                $personEntity->birthAddress = $birthAddressEntity;
            }

            if ($personEntity->deathTownId) {
                $deathTownEntity->country = $deathCountryEntity;
                $personEntity->deathTown = $deathTownEntity;
            }

            if ($personEntity->deathAddressId) {
                $deathAddressEntity->town = $deathTownEntity;
                $personEntity->deathAddress = $deathAddressEntity;
            }

            if ($personEntity->gravedTownId) {
                $gravedTownEntity->country = $gravedCountryEntity;
                $personEntity->gravedTown = $gravedTownEntity;
            }

            if ($personEntity->gravedAddressId) {
                $gravedAddressEntity->town = $gravedTownEntity;
                $personEntity->gravedAddress = $gravedAddressEntity;
            }

            $persons[$personEntity->id] = $personEntity;
        }

        return array_values($persons);
    }

    public function getByMotherId($motherId)
    {
        throw new NotImplementedException();
    }

    public function getMalesByMotherId($motherId)
    {
        throw new NotImplementedException();
    }

    public function getFemalesByMotherId($motherId)
    {
        throw new NotImplementedException();
    }

    public function getByFatherId($fatherId)
    {
        throw new NotImplementedException();
    }

    public function getMalesByFatherId($fatherId)
    {
        throw new NotImplementedException();
    }

    public function getFemalesByFatherId($fatherId)
    {
        throw new NotImplementedException();
    }

    /**
     * @param int $genusId
     *
     * @return PersonEntity[]
     */
    public function getByGenusId($genusId)
    {
        $rows = $this->getAllFluent()
            ->where('[p.genusId] = %i', $genusId)
            ->fetchAll();

        return $this->join($rows);
    }

    public function getByBirthTownId($townId)
    {
        throw new NotImplementedException();
    }

    public function getByBirthAddressId($addressId)
    {
        throw new NotImplementedException();
    }

    public function getByDeathTownId($townId)
    {
        throw new NotImplementedException();
    }

    public function getByDeathAddressId($addressId)
    {
        throw new NotImplementedException();
    }

    public function getByGravedTownId($townId)
    {
        throw new NotImplementedException();
    }

    public function getByGravedAddressId($addressId)
    {
        throw new NotImplementedException();
    }

    /**
     * @return PersonEntity[]
     */
    public function getAll()
    {
        $rows = $this->getAllFluent()->fetchAll();

        return $this->join($rows);
    }

    public function getMalesPairs()
    {
        throw new NotImplementedException();
    }

    public function getFemalesPairs()
    {
        throw new NotImplementedException();
    }

    public function getBrothers($fatherId, $motherId, $personId)
    {
        throw new NotImplementedException();
    }

    public function getSisters($fatherId, $motherId, $personId)
    {
        throw new NotImplementedException();
    }

    public function getSonsByPerson(PersonEntity $person)
    {
        throw new NotImplementedException();
    }

    public function getSonsById($id)
    {
        throw new NotImplementedException();
    }

    public function getDaughtersByPerson(PersonEntity $person)
    {
        throw new NotImplementedException();
    }

    public function getDaughtersById($id)
    {
        throw new NotImplementedException();
    }

    public function getChildrenByPerson(PersonEntity $person)
    {
        throw new NotImplementedException();
    }

    public function getChildrenById($id)
    {
        throw new NotImplementedException();
    }

    public function calculateAgeById($id)
    {
        throw new NotImplementedException();
    }

    public function calculateAgeByPerson(PersonEntity $person)
    {
        throw new NotImplementedException();
    }

    public function getByPrimaryKey($id)
    {
        $row = $this->getAllFluent()->
            where('[p.id] = %i', $id)
            ->fetch();

        return $this->join([$row])[0];
    }

    public function getByPrimaryKeys(array $ids)
    {
        $row = $this->getAllFluent()->
        where('[p.id] IN %in', $ids)
            ->fetch();

        return $this->join([$row])[0];
    }

    public function getColumnFluent($column)
    {
        throw new NotImplementedException();
    }

    public function getPairs($column)
    {
        throw new NotImplementedException();
    }

    public function getBySubQuery(Fluent $query)
    {
        $rows = $this->getAllFluent()
            ->where('[p.id] in %sql', $query)
            ->fetchAll();

        return $this->join($rows);
    }

    public function getAllPairs()
    {
        throw new NotImplementedException();
    }
}
