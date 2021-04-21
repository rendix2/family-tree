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

use Rendix2\FamilyTree\App\Model\CrudManager\DefaultContainer;
use Rendix2\FamilyTree\App\Model\Facades\DefaultFacade\DefaultFacade;
use Rendix2\FamilyTree\App\Model\Facades\Job\JobFacadeSelectRepository;
use Rendix2\FamilyTree\App\Model\Managers\Job\JobTable;
use Rendix2\FamilyTree\App\Model\Managers\JobManager;

/**
 * Class JobFacade
 *
 * @package Rendix2\FamilyTree\App\Model\Facades
 */
class JobFacade extends DefaultFacade
{
    /**
     * @var JobFacadeSelectRepository $jobFacadeSelectRepository
     */
    private $jobFacadeSelectRepository;

    /**
     * JobFacade constructor.
     *
     * @param DefaultContainer          $defaultContainer
     * @param JobFacadeSelectRepository $jobFacadeSelectRepository
     * @param JobTable                  $table
     * @param JobManager                $crudManager
     */
    public function __construct(
        DefaultContainer $defaultContainer,
        JobFacadeSelectRepository $jobFacadeSelectRepository,
        JobTable $table,
        JobManager $crudManager
    ) {
        parent::__construct($defaultContainer, $table, $crudManager);

        $this->jobFacadeSelectRepository = $jobFacadeSelectRepository;
    }

    public function __destruct()
    {
        $this->jobFacadeSelectRepository = null;

        parent::__destruct();
    }

    /**
     * @return JobFacadeSelectRepository
     */
    public function select()
    {
        return $this->jobFacadeSelectRepository;
    }
}
