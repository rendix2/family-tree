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
use Dibi\Fluent;
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
     * @param Connection $dibi
     * @param IStorage $storage
     * @param PersonManager $left
     */
    public function __construct(
        AddressManager $right,
        Connection $dibi,
        IStorage $storage,
        PersonManager $left
    ) {
        parent::__construct($dibi, $left, $right, $storage);
    }

    /**
     * @return Person2AddressEntity[]
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
     * @return Person2AddressEntity[]
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
     * @return Person2AddressEntity[]
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
     * @return Person2AddressEntity[]
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
     * @return Person2AddressEntity|false
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

    /**
     * @param string $column
     * @param Fluent $query
     *
     * @return Person2AddressEntity[]
     */
    public function getBySubQuery($column, Fluent $query)
    {
        return $this->getAllFluent()
            ->where('%n in %sql', $column, $query)
            ->execute()
            ->setRowClass(Person2AddressEntity::class)
            ->fetchAll();
    }
}
