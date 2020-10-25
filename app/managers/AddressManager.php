<?php
/**
 *
 * Created by PhpStorm.
 * Filename: s.php
 * User: Tomáš Babický
 * Date: 23.08.2020
 * Time: 15:11
 */

namespace Rendix2\FamilyTree\App\Managers;

use Dibi\Row;
use Rendix2\FamilyTree\App\Filters\AddressFilter;

/**
 * Class AddressManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class AddressManager extends CrudManager
{
    /**
     * @return Row[]
     */
    public function getAllJoinedCountryJoinedTown()
    {
        return $this->getDibi()
            ->select('a.*')
            ->select('c.name')
            ->as('countryName')
            ->select('t.name')
            ->as('townName')
            ->select('t.zipCode')
            ->as('townZipCode')
            ->from($this->getTableName())
            ->as('a')
            ->innerJoin(Tables::COUNTRY_TABLE)
            ->as('c')
            ->on('[a.countryId] = [c.id]')
            ->innerJoin(Tables::TOWN_TABLE)
            ->as('t')
            ->on('[a.townId] = [t.id]')
            ->fetchAll();
    }

    /**
     * @return array
     */
    public function getCountByTown()
    {
        return $this->dibi
            ->select('COUNT(%n)', $this->getPrimaryKey())
            ->select('town')
            ->from($this->getTableName())
            ->groupBy('town')
            ->fetchAll();
    }

    /**
     * @return array
     */
    public function getAllPairs()
    {
        $addressFilter = new AddressFilter();

        $addresses = $this->getAllJoinedCountryJoinedTown();
        $resultAddresses = [];

        foreach ($addresses as $address) {
            $resultAddresses[$address->id] = $addressFilter($address);
        }

        return $resultAddresses;
    }

    /**
     * @param int $id address ID
     *
     * @return Row
     */
    public function getByPrimaryKeyJoinedCountryJoinedTown($id)
    {
        return $this->getDibi()
            ->select('a.*')
            ->select('c.name')
            ->as('countryName')
            ->select('t.name')
            ->as('townName')
            ->select('t.zipCode')
            ->as('townZipCode')
            ->from($this->getTableName())
            ->as('a')
            ->innerJoin(Tables::COUNTRY_TABLE)
            ->as('c')
            ->on('[a.countryId] = [c.id]')
            ->innerJoin(Tables::TOWN_TABLE)
            ->as('t')
            ->on('[a.townId] = [t.id]')
            ->where('[a.id] = %i', $id)
            ->fetch();
    }

    /**
     * @param int $id country ID
     *
     * @return Row[]
     */
    public function getAllByCountryJoinedTown($id)
    {
        return $this->getDibi()
            ->select('a.*')
            ->select('t.name')
            ->as('townName')
            ->select('t.zipCode')
            ->as('townZipCode')
            ->from($this->getTableName())
            ->as('a')
            ->innerJoin(Tables::TOWN_TABLE)
            ->as('t')
            ->on('[a.townId] = [t.id]')
            ->where('[a.countryId] = %i', $id)
            ->fetchAll();
    }
}
