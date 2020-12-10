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
use Rendix2\FamilyTree\App\Model\Entities\Person2JobEntity;

/**
 * Class Person2JobManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class Person2JobManager extends M2NManager
{

    /**
     * Person2JobManager constructor.
     *
     * @param Connection $dibi
     * @param JobManager $right
     * @param PersonManager $left
     * @param IStorage $storage
     */
    public function __construct(
        Connection $dibi,
        JobManager $right,
        PersonManager $left,
        IStorage $storage
    ) {
        parent::__construct($dibi, $left, $right, $storage);
    }

    /**
     * @return Person2JobEntity[]
     */
    public function getAll()
    {
        return $this->getAllFluent()
            ->execute()
            ->setRowClass(Person2JobEntity::class)
            ->fetchAll();
    }

    /**
     * @param int $rightId
     *
     * @return Person2JobEntity[]
     */
    public function getAllByRight($rightId)
    {
        return $this->getFluentByRight($rightId)
            ->execute()
            ->setRowClass(Person2JobEntity::class)
            ->fetchAll();
    }

    /**
     * @param int $leftId
     *
     * @return Person2JobEntity[]
     */
    public function getAllByLeft($leftId)
    {
        return $this->getFluentByLeft($leftId)
            ->execute()
            ->setRowClass(Person2JobEntity::class)
            ->fetchAll();
    }

    /**
     * @param int $personId
     * @param int $jobId
     *
     * @return Person2JobEntity|false
     */
    public function getByLeftIdAndRightId($personId, $jobId)
    {
        return $this->getAllFLuent()
            ->where('%n = %i', $this->getLeftKey(), $personId)
            ->where('%n = %i', $this->getRightKey(), $jobId)
            ->execute()
            ->setRowClass(Person2JobEntity::class)
            ->fetch();
    }
}
