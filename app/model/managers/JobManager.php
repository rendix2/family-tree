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
use Nette\Http\IRequest;
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Model\Entities\JobEntity;

/**
 * Class JobManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class JobManager extends CrudManager
{
    /**
     * @var JobFilter $jobFilter
     */
    private $jobFilter;

    /**
     * JobManager constructor.
     *
     * @param Connection $dibi
     * @param IRequest $request
     * @param IStorage $storage
     * @param JobFilter $jobFilter
     */
    public function __construct(
        Connection $dibi,
        IRequest $request,
        IStorage $storage,
        JobFilter $jobFilter
    ) {
        parent::__construct($dibi, $request, $storage);

        $this->jobFilter = $jobFilter;
    }


    /**
     * @return JobEntity[]
     */
    public function getAll()
    {
        return $this->getAllFluent()
            ->execute()
            ->setRowClass(JobEntity::class)
            ->fetchAll();
    }

    /**
     * @param int $id
     *
     * @return JobEntity|false
     */
    public function getByPrimaryKey($id)
    {
        return $this->getAllFluent()
            ->where('%n = %i', $this->getPrimaryKey(), $id)
            ->execute()
            ->setRowClass(JobEntity::class)
            ->fetch();
    }

    /**
     * @param array $ids
     *
     * @return JobEntity[]|false
     */
    public function getByPrimaryKeys(array $ids)
    {
        $result = $this->checkValues($ids);

        if ($result !== null) {
            return $result;
        }

        return $this->getAllFluent()
            ->where('%n in %in', $this->getPrimaryKey(), $ids)
            ->execute()
            ->setRowClass(JobEntity::class)
            ->fetchAll();
    }

    /**
     * @param Fluent $query
     *
     * @return JobEntity[]
     */
    public function getBySubQuery(Fluent $query)
    {
        return $this->getAllFluent()
            ->where('%n in %sql', $this->getPrimaryKey(), $query)
            ->execute()
            ->setRowClass(JobEntity::class)
            ->fetchAll();
    }

    /**
     * @param int $townId town ID
     *
     * @return JobEntity[]
     */
    public function getByTownId($townId)
    {
        return $this->getAllFluent()
            ->where('[townId] = %i', $townId)
            ->execute()
            ->setRowClass(JobEntity::class)
            ->fetchAll();
    }

    /**
     * @param int $addressId address ID
     *
     * @return JobEntity[]
     */
    public function getByAddressId($addressId)
    {
        return $this->getAllFluent()
            ->where('[addressId] = %i', $addressId)
            ->execute()
            ->setRowClass(JobEntity::class)
            ->fetchAll();
    }

    /**
     * @param int $addressId
     *
     * @return JobEntity[]
     */
    public function getByAddressIdCached($addressId)
    {
        return $this->getCache()->call([$this, 'getByAddressId'], $addressId);
    }

    /**
     * @return array
     */
    public function getAllPairs()
    {
        $jobFilter = $this->jobFilter;

        $jobs = $this->getAll();
        $resultJobs = [];

        foreach ($jobs as $job) {
            $resultJobs[$job->id] = $jobFilter($job);
        }

        return $resultJobs;
    }

    /**
     * @return array
     */
    public function getAllPairsCached()
    {
        return $this->getCache()->call([$this, 'getAllPairs']);
    }
}
