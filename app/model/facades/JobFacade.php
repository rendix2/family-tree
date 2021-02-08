<?php
/**
 *
 * Created by PhpStorm.
 * Filename: JobFacade.php
 * User: Tomáš Babický
 * Date: 16.11.2020
 * Time: 0:01
 */

namespace Rendix2\FamilyTree\App\Model\Facades;

use Dibi\Fluent;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Managers\JobManager;
use Rendix2\FamilyTree\App\Model\Entities\AddressEntity;
use Rendix2\FamilyTree\App\Model\Entities\JobEntity;
use Rendix2\FamilyTree\App\Model\Entities\TownEntity;

/**
 * Class JobFacade
 *
 * @package Rendix2\FamilyTree\App\Model\Facades
 */
class JobFacade
{
    use GetIds;

    /**
     * @var Cache $cache
     */
    private $cache;

    /**
     * @var JobManager $jobManager
     */
    private $jobManager;

    /**
     * @var TownFacade $townFacade
     */
    private $townFacade;

    /***
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * JobFacade constructor.
     *
     * @param AddressFacade $addressFacade
     * @param IStorage $storage
     * @param JobManager $jobManager
     * @param TownFacade $townFacade
     */
    public function __construct(
        AddressFacade $addressFacade,
        IStorage $storage,
        JobManager $jobManager,
        TownFacade $townFacade
    ) {
        $this->addressFacade = $addressFacade;
        $this->cache = new Cache($storage, self::class);
        $this->jobManager = $jobManager;
        $this->townFacade = $townFacade;
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

    /**
     * @return JobEntity[]
     */
    public function getAll()
    {
        $jobs = $this->jobManager->getAll();

        $addressId = $this->getIds($jobs, '_addressId');
        $townId = $this->getIds($jobs, '_townId');

        $towns = $this->townFacade->getByPrimaryKeys($townId);
        $addresses = $this->addressFacade->getByPrimaryKeys($addressId);

        return $this->join($jobs, $towns, $addresses);
    }

    /**
     * @return JobEntity[]
     */
    public function getAllCached()
    {
        return $this->cache->call([$this, 'getAll']);
    }

    /**
     * @return JobEntity[]
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
     * @return JobEntity[]
     */
    public function getPairsCached()
    {
        return $this->cache->call([$this, 'getAllPairs']);
    }

    /**
     * @param int $jobId
     *
     * @return JobEntity
     */
    public function getByPrimaryKey($jobId)
    {
        $job = $this->jobManager->getByPrimaryKey($jobId);

        if (!$job) {
            return null;
        }

        $town = [];

        if ($job->_townId) {
            $town[] = $this->townFacade->getByPrimaryKey($job->_townId);
        }

        $address = [];

        if ($job->_addressId) {
            $address[] = $this->addressFacade->getByPrimaryKey($job->_addressId);
        }

        return $this->join([$job], $town, $address)[0];
    }

    /**
     * @param int $jobId
     *
     * @return JobEntity
     */
    public function getByPrimaryKeyCached($jobId)
    {
        return $this->cache->call([$this, 'getByPrimaryKey'], $jobId);
    }

    /**
     * @param array $jobIds
     *
     * @return JobEntity[]
     */
    public function getByPrimaryKeys($jobIds)
    {
        $jobs = $this->jobManager->getByPrimaryKeys($jobIds);

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

        $towns = $this->townFacade->getByPrimaryKeys($townIds);
        $addresses = $this->addressFacade->getByPrimaryKeys($addressIds);

        return $this->join($jobs, $towns, $addresses);
    }

    /**
     * @param array $jobIds
     *
     * @return JobEntity[]
     */
    public function getByPrimaryKeysCached(array $jobIds)
    {
        return $this->cache->call([$this, 'getByPrimaryKeys'], $jobIds);
    }

    /**
     * @param int $townId
     *
     * @return JobEntity[]
     */
    public function getByTownId($townId)
    {
        $jobs = $this->jobManager->getByTownId($townId);
        $town = $this->townFacade->getByPrimaryKey($townId);
        $addresses = $this->addressFacade->getByTownId($townId);

        return $this->join($jobs, [$town], $addresses);
    }

    /**
     * @param int $townId
     *
     * @return JobEntity[]
     */
    public function getByTownIdCached($townId)
    {
        return $this->cache->call([$this, 'getByTownId'], $townId);
    }

    /**
     * @param Fluent $query
     *
     * @return JobEntity[]
     */
    public function getBySubQuery(Fluent $query)
    {
        $jobs = $this->jobManager->getBySubQuery($query);

        $addressId = $this->getIds($jobs, '_addressId');
        $townId = $this->getIds($jobs, '_townId');

        $towns = $this->townFacade->getByPrimaryKeys($townId);
        $addresses = $this->addressFacade->getByPrimaryKeys($addressId);

        return $this->join($jobs, $towns, $addresses);
    }
}
