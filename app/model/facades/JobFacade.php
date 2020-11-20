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

use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Managers\JobManager;
use Rendix2\FamilyTree\App\Model\Entities\AddressEntity;
use Rendix2\FamilyTree\App\Model\Entities\JobEntity;
use Rendix2\FamilyTree\App\Model\Entities\TownEntity;

class JobFacade
{
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
        $towns = $this->townFacade->getAll();
        $addresses = $this->addressFacade->getAll();

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

        $towns = $this->townFacade->getAll();
        $addresses = $this->addressFacade->getAll();

        return $this->join([$job], $towns, $addresses)[0];
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
}
