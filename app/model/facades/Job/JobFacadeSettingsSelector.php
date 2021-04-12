<?php
/**
 *
 * Created by PhpStorm.
 * Filename: JobFacadeSettingsSelector.php
 * User: Tomáš Babický
 * Date: 09.04.2021
 * Time: 14:06
 */

namespace Rendix2\FamilyTree\App\Model\Facades\Job;

use Dibi\Fluent;

/**
 * Class JobFacadeSettingsSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Facades\Job
 */
class JobFacadeSettingsSelector extends JobFacadeSelector
{
    public function getByTownId($townId)
    {
        $jobs = $this->getJobManager()->select()->getSettingsManager()->getByTownId($townId);
        $town = $this->getTownFacade()->select()->getSettingsManager()->getByPrimaryKey($townId);
        $addresses = $this->getAddressFacade()->select()->getManager()->getByTownId($townId);

        return $this->join($jobs, [$town], $addresses);
    }

    public function getByPrimaryKey($id)
    {
        $job = $this->getJobManager()->select()->getSettingsManager()->getByPrimaryKey($id);

        if (!$job) {
            return null;
        }

        $town = [];

        if ($job->_townId) {
            $town[] = $this->getTownFacade()->select()->getSettingsManager()->getByPrimaryKey($job->_townId);
        }

        $address = [];

        if ($job->_addressId) {
            $address[] = $this->getAddressFacade()->select()->getManager()->getByPrimaryKey($job->_addressId);
        }

        return $this->join([$job], $town, $address)[0];
    }

    public function getByPrimaryKeys(array $ids)
    {
        $jobs = $this->getJobManager()->select()->getSettingsManager()->getByPrimaryKeys($ids);

        if (!$jobs) {
            return [];
        }

        $townIds = [];
        $addressIds = [];

        foreach ($jobs as $job) {
            $townIds[] = $job->_townId;
            $addressIds[] = $job->_addressId;
        }

        $townIds = array_unique($townIds);
        $addressIds = array_unique($addressIds);

        $towns = $this->getTownFacade()->select()->getSettingsManager()->getByPrimaryKeys($townIds);
        $addresses = $this->getAddressFacade()->select()->getManager()->getByPrimaryKeys($addressIds);

        return $this->join($jobs, $towns, $addresses);
    }

    public function getAll()
    {
        $jobs = $this->getJobManager()->select()->getSettingsManager()->getAll();

        $addressId = $this->getIds($jobs, '_addressId');
        $townId = $this->getIds($jobs, '_townId');

        $towns = $this->getTownFacade()->select()->getSettingsManager()->getByPrimaryKeys($townId);
        $addresses = $this->getAddressFacade()->select()->getManager()->getByPrimaryKeys($addressId);

        return $this->join($jobs, $towns, $addresses);
    }

    public function getAllPairs()
    {
        $jobFilter = $this->getJobFilter();

        $jobs = $this->getAll();
        $resultJobs = [];

        foreach ($jobs as $job) {
            $resultJobs[$job->id] = $jobFilter($job);
        }

        return $resultJobs;
    }

    public function getBySubQuery(Fluent $query)
    {
        $jobs = $this->getJobManager()->select()->getSettingsManager()->getBySubQuery($query);

        $addressId = $this->getIds($jobs, '_addressId');
        $townId = $this->getIds($jobs, '_townId');

        $towns = $this->getTownFacade()->select()->getSettingsManager()->getByPrimaryKeys($townId);
        $addresses = $this->getAddressFacade()->select()->getManager()->getByPrimaryKeys($addressId);

        return $this->join($jobs, $towns, $addresses);
    }
}
