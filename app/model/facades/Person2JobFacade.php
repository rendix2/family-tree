<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonJobFacade.php
 * User: Tomáš Babický
 * Date: 10.11.2020
 * Time: 3:02
 */

namespace Rendix2\FamilyTree\App\Model\Facades;

use Rendix2\FamilyTree\App\Model\CrudManager\DefaultContainer;
use Rendix2\FamilyTree\App\Model\Facades\DefaultFacade\DefaultFacade;
use Rendix2\FamilyTree\App\Model\Facades\Person2Job\Person2JobFacadeSelectRepository;
use Rendix2\FamilyTree\App\Model\Managers\Person2JobManager;
use Rendix2\FamilyTree\App\Model\Tables\Person2JobTable;

/**
 * Class Person2JobFacade
 *
 * @package Rendix2\FamilyTree\App\Model\Facades
 */
class Person2JobFacade extends DefaultFacade
{
    /**
     * @var Person2JobFacadeSelectRepository $person2JobFacadeSelectRepository
     */
    private $person2JobFacadeSelectRepository;

    /**
     * Person2JobFacade constructor.
     *
     * @param DefaultContainer                 $defaultContainer
     * @param Person2JobFacadeSelectRepository $person2JobFacadeSelectRepository
     * @param Person2JobTable                  $table
     * @param Person2JobManager                $crudManager
     */
    public function __construct(
        DefaultContainer $defaultContainer,
        Person2JobFacadeSelectRepository $person2JobFacadeSelectRepository,
        Person2JobTable $table,
        Person2JobManager $crudManager
    ) {
        parent::__construct($defaultContainer, $table, $crudManager);

        $this->person2JobFacadeSelectRepository = $person2JobFacadeSelectRepository;
    }

    public function __destruct()
    {
        $this->person2JobFacadeSelectRepository = null;

        parent::__destruct();
    }

    /**
     * @return Person2JobFacadeSelectRepository
     */
    public function select()
    {
        return $this->person2JobFacadeSelectRepository;
    }
}

