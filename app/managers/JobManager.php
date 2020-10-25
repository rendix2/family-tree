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

/**
 * Class JobManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class JobManager extends CrudManager
{
    /**
     * @param int $townId town ID
     * @return array
     */
    public function getByTownId($townId)
    {
        return $this->getAllFluent()
            ->where('[townId] = %i', $townId)
            ->fetchAll();
    }

    /**
     * @param int $addressId address ID
     * @return array
     */
    public function getByAddressId($addressId)
    {
        return $this->getAllFluent()
            ->where('[addressId] = %i', $addressId)
            ->fetchAll();
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
}
