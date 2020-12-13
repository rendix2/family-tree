<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonJobFacade.php
 * User: Tomáš Babický
 * Date: 10.11.2020
 * Time: 3:02
 */

namespace Rendix2\FamilyTree\App\Facades;

use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Managers\JobManager;
use Rendix2\FamilyTree\App\Managers\Person2JobManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Model\Entities\DurationEntity;
use Rendix2\FamilyTree\App\Model\Entities\JobEntity;
use Rendix2\FamilyTree\App\Model\Entities\Person2JobEntity;
use Rendix2\FamilyTree\App\Model\Entities\PersonEntity;
use Rendix2\FamilyTree\App\Model\Facades\GetIds;
use Rendix2\FamilyTree\App\Model\Facades\JobFacade;

/**
 * Class Person2JobFacade
 *
 * @package Rendix2\FamilyTree\App\Facades
 */
class Person2JobFacade
{
    use GetIds;

    /**
     * @var Cache $cache
     */
    private $cache;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var Person2JobManager $person2JobManager
     */
    private $person2JobManager;

    /**
     * @var JobFacade $jobFacade
     */
    private $jobFacade;

    /**
     * @var JobManager $jobManager
     */
    private $jobManager;

    /**
     * PersonJobFacade constructor.
     *
     * @param IStorage $storage
     * @param JobFacade $jobFacade
     * @param JobManager $jobManager
     * @param Person2JobManager $person2JobManager
     * @param PersonFacade $personFacade
     * @param PersonManager $personManager
     */
    public function __construct(
        IStorage $storage,
        JobFacade $jobFacade,
        JobManager $jobManager,
        Person2JobManager $person2JobManager,
        PersonFacade $personFacade,
        PersonManager  $personManager
    ) {
        $this->cache = new Cache($storage, self::class);
        $this->person2JobManager = $person2JobManager;
        $this->personFacade = $personFacade;
        $this->personManager = $personManager;
        $this->jobFacade = $jobFacade;
        $this->jobManager = $jobManager;
    }

    /**
     * @param Person2JobEntity[] $relations
     * @param PersonEntity[] $persons
     * @param JobEntity[] $jobs
     *
     * @return Person2JobEntity[]
     */
    public function join(array $relations, array $persons, array $jobs)
    {
        foreach ($relations as $relation) {
            foreach ($persons as $person) {
                if ($relation->_personId === $person->id) {
                    $relation->person = $person;
                    break;
                }
            }

            foreach ($jobs as $job) {
                if ($relation->_jobId === $job->id) {
                    $relation->job = $job;
                    break;
                }
            }

            $duration = new DurationEntity((array) $relation);
            $relation->duration = $duration;
            $relation->clean();
        }

        return $relations;
    }

    /**
     * @return Person2JobEntity[]
     */
    public function getAll()
    {
        $relations = $this->person2JobManager->getAll();

        $personIds = $this->person2JobManager->getColumnFluent('personId');
        $jobIds = $this->person2JobManager->getColumnFluent('jobId');

        $persons = $this->personFacade->getBySubQuery($personIds);
        $jobs = $this->jobFacade->getBySubQuery($jobIds);

        return $this->join($relations, $persons, $jobs);
    }

    /**
     * @return Person2JobEntity[]
     */
    public function getAllCached()
    {
        return $this->cache->call([$this, 'getAll']);
    }

    /**
     * @param int $personId
     *
     * @return Person2JobEntity[]
     */
    public function getByLeft($personId)
    {
        $relations = $this->person2JobManager->getAllByLeft($personId);

        $relationJobIds = $this->getIds($relations, '_jobId');

        $person = $this->personFacade->getByPrimaryKey($personId);
        $jobs = $this->jobFacade->getByPrimaryKeys($relationJobIds);

        return $this->join($relations, [$person], $jobs);
    }

    /**
     * @param int $personId
     *
     * @return Person2JobEntity[]
     */
    public function getByLeftCached($personId)
    {
        return $this->cache->call([$this, 'getByLeft'], $personId);
    }

    /**
     * @param int $jobId
     *
     * @return Person2JobEntity[]
     */
    public function getByRight($jobId)
    {
        $relations = $this->person2JobManager->getAllByRight($jobId);

        $relationPersonsIds = [];

        foreach ($relations as $relation) {
            $relationPersonsIds[] = $relation->_personId;
        }

        $persons = $this->personFacade->getByPrimaryKeys($relationPersonsIds);
        $job = $this->jobFacade->getByPrimaryKey($jobId);

        return $this->join($relations, $persons, [$job]);
    }

    /**
     * @param int $personId
     *
     * @return Person2JobEntity[]
     */
    public function getByRightCached($personId)
    {
        return $this->cache->call([$this, 'getByRight'], $personId);
    }

    /**
     * @param int $personId
     * @param int $jobId
     *
     * @return Person2JobEntity
     */
    public function getByLeftAndRight($personId, $jobId)
    {
        $relation = $this->person2JobManager->getByLeftIdAndRightId($personId, $jobId);
        $person = $this->personFacade->getByPrimaryKey($personId);
        $job = $this->jobFacade->getByPrimaryKey($jobId);

        return $this->join([$relation], [$person], [$job])[0];
    }

    /**
     * @param int $personId
     * @param int $jobId
     *
     * @return Person2JobEntity
     */
    public function getByLeftAndRightCached($personId, $jobId)
    {
        return $this->cache->call([$this, 'getByLeftAndRight'], $personId, $jobId);
    }
}

