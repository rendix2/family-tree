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

use Dibi\Connection;
use Dibi\Row;
use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Model\Entities\Person2AddressEntity;

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
     * @param AddressManager $right
     * @param BackupManager $backupManager
     * @param Connection $dibi
     * @param IStorage $storage
     * @param PersonManager $left
     */
    public function __construct(
        AddressManager $right,
        BackupManager $backupManager,
        Connection $dibi,
        IStorage $storage,
        PersonManager $left
    ) {
        parent::__construct($backupManager,$dibi, $left, $right, $storage);
    }

    /**
     * @return Row[]
     */
    public function getAll()
    {
        return $this->getAllFluent()
            ->execute()
            ->setRowClass(Person2AddressEntity::class)
            ->fetchAll();
    }

    /**
     * @param int $leftId
     *
     * @return array
     */
    public function getAllByLeft($leftId)
    {
        return $this->getFluentByLeft($leftId)
            ->execute()
            ->setRowClass(Person2AddressEntity::class)
            ->fetchAll();
    }

    /**
     * @param int $rightId
     *
     * @return array
     */
    public function getAllByRightJoined($rightId)
    {
        return $this->getFluentByRightJoined($rightId)
            ->execute()
            ->setRowClass(Person2AddressEntity::class)
            ->fetchAll();
    }

    /**
     * @param int $rightId
     *
     * @return array
     */
    public function getAllByRight($rightId)
    {
        return $this->getFluentByRight($rightId)
            ->execute()
            ->setRowClass(Person2AddressEntity::class)
            ->fetchAll();
    }

    /**
     * @param int $personId
     * @param int $addressId
     *
     * @return Row|false
     */
    public function getByLeftIdAndRightId($personId, $addressId)
    {
        return $this->getAllFLuent()
            ->where('%n = %i', $this->getLeftKey(), $personId)
            ->where('%n = %i', $this->getRightKey(), $addressId)
            ->execute()
            ->setRowClass(Person2AddressEntity::class)
            ->fetch();
    }

}
