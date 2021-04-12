<?php
/**
 *
 * Created by PhpStorm.
 * Filename: JobFacadeSelector.php
 * User: Tomáš Babický
 * Date: 09.04.2021
 * Time: 14:05
 */

namespace Rendix2\FamilyTree\App\Model\Facades\Job;

use Dibi\Fluent;
use Nette\NotImplementedException;
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Model\Entities\AddressEntity;
use Rendix2\FamilyTree\App\Model\Entities\JobEntity;
use Rendix2\FamilyTree\App\Model\Entities\TownEntity;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Model\Facades\DefaultFacade\DefaultFacadeSelector;
use Rendix2\FamilyTree\App\Model\Facades\TownFacade;
use Rendix2\FamilyTree\App\Model\Managers\Job\Interfaces\IJobSelector;
use Rendix2\FamilyTree\App\Model\Managers\JobManager;

class JobFacadeSelector extends DefaultFacadeSelector implements IJobSelector
{
    /***
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * @var JobFilter $jobFilter
     */
    private $jobFilter;

    /**
     * @var JobManager $jobManager
     */
    private $jobManager;

    /**
     * @var TownFacade $townFacade
     */
    private $townFacade;

    /**
     * JobFacade constructor.
     *
     * @param AddressFacade $addressFacade
     * @param JobManager $jobManager
     * @param JobFilter $jobFilter
     * @param TownFacade $townFacade
     */
    public function __construct(
        AddressFacade $addressFacade,
        JobManager $jobManager,
        JobFilter $jobFilter,
        TownFacade $townFacade
    ) {
        parent::__construct($jobFilter);

        $this->addressFacade = $addressFacade;
        $this->townFacade = $townFacade;

        $this->jobFilter = $jobFilter;
        $this->jobManager = $jobManager;
    }

    /**
     * @return AddressFacade
     */
    public function getAddressFacade()
    {
        return $this->addressFacade;
    }

    /**
     * @return JobFilter
     */
    public function getJobFilter()
    {
        return $this->jobFilter;
    }

    /**
     * @return JobManager
     */
    public function getJobManager()
    {
        return $this->jobManager;
    }

    /**
     * @return TownFacade
     */
    public function getTownFacade()
    {
        return $this->townFacade;
    }

    /**
     * @param JobEntity[] $jobs
     * @param TownEntity[] $towns
     * @param AddressEntity[] $addresses
     *
     * @return JobEntity[]
     */
    public function join(array $jobs, array $towns, array $addresses)
    {
        foreach ($jobs as $job) {
            foreach ($towns as $town) {
                if ($job->_townId === $town->id) {
                    $job->town = $town;
                    break;
                }
            }

            foreach ($addresses as $address) {
                if ($job->_addressId === $address->id) {
                    $job->address = $address;
                    break;
                }
            }

            $job->clean();
        }

        return $jobs;
    }

    public function getByTownId($townId)
    {
        $jobs = $this->jobManager->select()->getManager()->getByTownId($townId);
        $town = $this->townFacade->select()->getManager()->getByPrimaryKey($townId);
        $addresses = $this->addressFacade->select()->getManager()->getByTownId($townId);

        return $this->join($jobs, [$town], $addresses);
    }

    public function getByAddressId($addressId)
    {
        throw new NotImplementedException();
    }

    public function getByPrimaryKey($id)
    {
        $job = $this->jobManager->select()->getManager()->getByPrimaryKey($id);

        if (!$job) {
            return null;
        }

        $town = [];

        if ($job->_townId) {
            $town[] = $this->townFacade->select()->getCachedManager()->getByPrimaryKey($job->_townId);
        }

        $address = [];

        if ($job->_addressId) {
            $address[] = $this->addressFacade->select()->getCachedManager()->getByPrimaryKey($job->_addressId);
        }

        return $this->join([$job], $town, $address)[0];
    }

    public function getByPrimaryKeys(array $ids)
    {
        $jobs = $this->jobManager->select()->getManager()->getByPrimaryKeys($ids);

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

        $towns = $this->townFacade->select()->getManager()->getByPrimaryKeys($townIds);
        $addresses = $this->addressFacade->select()->getManager()->getByPrimaryKeys($addressIds);

        return $this->join($jobs, $towns, $addresses);
    }

    public function getColumnFluent($column)
    {
        throw new NotImplementedException();
    }

    public function getAll()
    {
        $jobs = $this->jobManager->select()->getCachedManager()->getAll();

        $addressId = $this->getIds($jobs, '_addressId');
        $townId = $this->getIds($jobs, '_townId');

        $towns = $this->townFacade->select()->getManager()->getByPrimaryKeys($townId);
        $addresses = $this->addressFacade->select()->getManager()->getByPrimaryKeys($addressId);

        return $this->join($jobs, $towns, $addresses);
    }

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

    public function getPairs($column)
    {
        throw new NotImplementedException();
    }

    public function getBySubQuery(Fluent $query)
    {
        $jobs = $this->jobManager->select()->getManager()->getBySubQuery($query);

        $addressId = $this->getIds($jobs, '_addressId');
        $townId = $this->getIds($jobs, '_townId');

        $towns = $this->townFacade->select()->getManager()->getByPrimaryKeys($townId);
        $addresses = $this->addressFacade->select()->getManager()->getByPrimaryKeys($addressId);

        return $this->join($jobs, $towns, $addresses);
    }
}
