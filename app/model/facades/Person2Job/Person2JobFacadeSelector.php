<?php
/**
 *
 * Created by PhpStorm.
 * Filename: Person2JobFacadeSelector.php
 * User: Tomáš Babický
 * Date: 12.04.2021
 * Time: 0:56
 */

namespace Rendix2\FamilyTree\App\Model\Facades\Person2Job;

use Nette\NotImplementedException;
use Rendix2\FamilyTree\App\Model\Entities\DurationEntity;
use Rendix2\FamilyTree\App\Model\Entities\JobEntity;
use Rendix2\FamilyTree\App\Model\Entities\Person2JobEntity;
use Rendix2\FamilyTree\App\Model\Entities\PersonEntity;
use Rendix2\FamilyTree\App\Model\Facades\DefaultFacade\DefaultFacadeSelector;
use Rendix2\FamilyTree\App\Model\Facades\JobFacade;
use Rendix2\FamilyTree\App\Model\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Model\Managers\M2NManger\Interfaces\IM2NSelector;
use Rendix2\FamilyTree\App\Model\Managers\Person2JobManager;

/**
 * Class Person2JobFacadeSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Facades\Person2Job
 */
class Person2JobFacadeSelector extends DefaultFacadeSelector implements IM2NSelector
{
    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var Person2JobManager $person2JobManager
     */
    private $person2JobManager;

    /**
     * @var JobFacade $jobFacade
     */
    private $jobFacade;

    /**
     * PersonJobFacade constructor.
     *
     * @param JobFacade $jobFacade
     * @param Person2JobManager $person2JobManager
     * @param PersonFacade $personFacade
     */
    public function __construct(
        JobFacade $jobFacade,
        Person2JobManager $person2JobManager,
        PersonFacade $personFacade
    ) {
        parent::__construct();

        $this->person2JobManager = $person2JobManager;
        $this->personFacade = $personFacade;
        $this->jobFacade = $jobFacade;
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
        $relations = $this->person2JobManager->select()->getCachedManager()->getAll();

        $personIds = $this->person2JobManager->select()->getManager()->getColumnFluent('personId');
        $jobIds = $this->person2JobManager->select()->getManager()->getColumnFluent('jobId');

        $persons = $this->personFacade->select()->getManager()->getBySubQuery($personIds);
        $jobs = $this->jobFacade->select()->getManager()->getBySubQuery($jobIds);

        return $this->join($relations, $persons, $jobs);
    }

    public function getColumnFluent($column)
    {
        throw new NotImplementedException();
    }

    public function getByLeftKey($leftId)
    {
        $relations = $this->person2JobManager->select()->getManager()->getByLeftKey($leftId);

        $relationJobIds = $this->getIds($relations, '_jobId');

        $person = $this->personFacade->select()->getManager()->getByPrimaryKey($leftId);
        $jobs = $this->jobFacade->select()->getManager()->getByPrimaryKeys($relationJobIds);

        return $this->join($relations, [$person], $jobs);
    }

    public function getPairsByLeft($leftId)
    {
        throw new NotImplementedException();
    }

    public function getByLeftKeyJoined($leftId)
    {
        throw new NotImplementedException();
    }

    public function getByRightKey($rightId)
    {
        $relations = $this->person2JobManager->select()->getManager()->getByRightKey($rightId);

        $relationPersonsIds = [];

        foreach ($relations as $relation) {
            $relationPersonsIds[] = $relation->_personId;
        }

        $persons = $this->personFacade->select()->getManager()->getByPrimaryKeys($relationPersonsIds);
        $job = $this->jobFacade->select()->getManager()->getByPrimaryKey($rightId);

        return $this->join($relations, $persons, [$job]);
    }

    public function getPairsByRight($rightId)
    {
        throw new NotImplementedException();
    }

    public function getByRightKeyJoined($rightId)
    {
        throw new NotImplementedException();
    }

    public function getByLeftAndRightKey($leftId, $rightId)
    {
        $relation = $this->person2JobManager->select()->getManager()->getByLeftAndRightKey($leftId, $rightId);
        $person = $this->personFacade->select()->getManager()->getByPrimaryKey($leftId);
        $job = $this->jobFacade->select()->getManager()->getByPrimaryKey($rightId);

        return $this->join([$relation], [$person], [$job])[0];
    }
}