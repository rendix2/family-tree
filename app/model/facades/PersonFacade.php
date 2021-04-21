<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonFacade.php
 * User: Tomáš Babický
 * Date: 10.11.2020
 * Time: 2:42
 */

namespace Rendix2\FamilyTree\App\Model\Facades;

use Rendix2\FamilyTree\App\Model\CrudManager\DefaultContainer;
use Rendix2\FamilyTree\App\Model\Facades\DefaultFacade\DefaultFacade;
use Rendix2\FamilyTree\App\Model\Facades\Person\PersonFacadeSelectRepository;
use Rendix2\FamilyTree\App\Model\Managers\Person\PersonTable;
use Rendix2\FamilyTree\App\Model\Managers\PersonManager;

/**
 * Class PersonFacade
 *
 * @package Rendix2\FamilyTree\App\Model\Facades
 */
class PersonFacade extends DefaultFacade
{
    /**
     * @var PersonFacadeSelectRepository $personFacadeSelectRepository
     */
    private $personFacadeSelectRepository;

    /**
     * PersonFacade constructor.
     *
     * @param DefaultContainer             $defaultContainer
     * @param PersonFacadeSelectRepository $personFacadeSelectRepository
     * @param PersonTable                  $table
     * @param PersonManager                $crudManager
     */
    public function __construct(
        DefaultContainer $defaultContainer,
        PersonFacadeSelectRepository $personFacadeSelectRepository,
        PersonTable $table,
        PersonManager $crudManager
    ) {
        parent::__construct($defaultContainer, $table, $crudManager);

        $this->personFacadeSelectRepository = $personFacadeSelectRepository;
    }

    public function __destruct()
    {
        $this->personFacadeSelectRepository = null;

        parent::__destruct();
    }

    /**
     * @return PersonFacadeSelectRepository
     */
    public function select()
    {
        return $this->personFacadeSelectRepository;
    }
}
