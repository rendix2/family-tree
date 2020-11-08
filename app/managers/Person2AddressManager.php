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

use dibi;
use Dibi\Connection;
use Dibi\Row;

/**
 * Class Person2AddressManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class Person2AddressManager extends M2NManager
{
    /**
     * Person2AddressManager constructor.
     *
     * @param Connection $dibi
     * @param PersonManager $left
     * @param AddressManager $right
     * @param BackupManager $backupManager
     */
    public function __construct(Connection $dibi, PersonManager $left, AddressManager $right, BackupManager $backupManager)
    {
        parent::__construct($dibi, $left, $right, $backupManager);
    }

    /**
     * @param int $leftId
     * @return Row[]
     */
    public function getAllByLeftJoinedCountryJoinedTownJoined($leftId)
    {
        return $this->dibi
            ->select('p2a.*')
            ->select('a.*')
            ->select('c.name')
            ->as('countryName')
            ->select('t.name')
            ->as('townName')
            ->select('t.zipCode')
            ->as('townZipCode')
            ->from($this->getTableName())
            ->as('p2a')
            ->innerJoin($this->getRightTable()->getTableName())
            ->as('a')
            ->on('%n = %n', 'p2a.' . $this->getRightKey(), 'a.' . $this->getRightTable()->getPrimaryKey())
            ->innerJoin(Tables::COUNTRY_TABLE)
            ->as('c')
            ->on('[a.countryId] = [c.id]')
            ->innerJoin(Tables::TOWN_TABLE)
            ->as('t')
            ->on('[a.townId] = [t.id]')
            ->where('%n = %i', $this->getLeftKey(), $leftId)
            ->orderBy('dateSince', dibi::ASC)
            ->fetchAll();
    }

    /**
     * @return Row[]
     */
    public function getAllJoinedCountryJoinedTownJoined()
    {
        return $this->dibi
            ->select('person2address.*')
            ->select('address.*')
            ->select('person.*')
            ->select('country.name')
            ->as('countryName')
            ->select('town.name')
            ->as('townName')
            ->select('town.zipCode')
            ->as('townZipCode')
            ->from($this->getTableName())
            ->innerJoin($this->getLeftTable()->getTableName())
            ->on('%n = %n', $this->getTableName(). '.' . $this->getLeftKey(), $this->getLeftTable()->getTableName() . '.'. $this->getLeftTable()->getPrimaryKey())
            ->innerJoin($this->getRightTable()->getTableName())
            ->on('%n = %n', $this->getTableName(). '.' . $this->getRightKey(), $this->getRightTable()->getTableName() . '.'. $this->getRightTable()->getPrimaryKey())
            ->innerJoin(Tables::COUNTRY_TABLE)
            ->on('[address.countryId] = [country.id]')
            ->innerJoin(Tables::TOWN_TABLE)
            ->on('[address.townId] = [town.id]')
            ->orderBy('dateSince', dibi::ASC)
            ->fetchAll();
    }
}
