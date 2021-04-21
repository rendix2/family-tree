<?php
/**
 *
 * Created by PhpStorm.
 * Filename: JobManager.php
 * User: Tomáš Babický
 * Date: 02.04.2021
 * Time: 15:05
 */

namespace Rendix2\FamilyTree\App\Model\Managers;

use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Model\CrudManager\CrudManager;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultContainer;
use Rendix2\FamilyTree\App\Model\Managers\Job\JobSelectRepository;
use Rendix2\FamilyTree\App\Model\Managers\Job\JobTable;

/**
 * Class JobManager
 *
 * @package Rendix2\FamilyTree\App\Model\Managers
 */
class JobManager extends CrudManager
{
    /**
     * @var JobSelectRepository $jobSelectRepository
     */
    private $jobSelectRepository;

    /**
     * JobManager constructor.
     *
     * @param DefaultContainer    $defaultContainer
     * @param JobSelectRepository $jobSelectRepository
     * @param JobTable            $table
     * @param JobFilter           $jobFilter
     */
    public function __construct(
        DefaultContainer $defaultContainer,
        JobSelectRepository  $jobSelectRepository,
        JobTable $table,
        JobFilter $jobFilter
    ) {
        parent::__construct($defaultContainer, $table, $jobFilter);

        $this->jobSelectRepository = $jobSelectRepository;
    }

    public function __destruct()
    {
        $this->jobSelectRepository = null;

        parent::__destruct();
    }


    /**
     * @return JobSelectRepository
     */
    public function select()
    {
        return $this->jobSelectRepository;
    }
}
