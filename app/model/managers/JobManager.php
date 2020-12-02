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
        $jobFilter = new JobFilter();

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
