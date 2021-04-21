<?php
/**
 *
 * Created by PhpStorm.
 * Filename: RelationFacade.php
 * User: Tomáš Babický
 * Date: 10.11.2020
 * Time: 2:45
 */

namespace Rendix2\FamilyTree\App\Model\Facades;



use Rendix2\FamilyTree\App\Model\CrudManager\DefaultContainer;
use Rendix2\FamilyTree\App\Model\Facades\DefaultFacade\DefaultFacade;
use Rendix2\FamilyTree\App\Model\Facades\Relation\RelationFacadeSelectRepository;
use Rendix2\FamilyTree\App\Model\Managers\Relation\RelationTable;
use Rendix2\FamilyTree\App\Model\Managers\RelationManager;

/**
 * Class RelationFacade
 *
 * @package Rendix2\FamilyTree\App\Facades
 */
class RelationFacade extends DefaultFacade
{
    /**
     * @var RelationFacadeSelectRepository $relationFacadeSelectRepository
     */
    private $relationFacadeSelectRepository;

    /**
     * RelationFacade constructor.
     *
     * @param DefaultContainer               $defaultContainer
     * @param RelationFacadeSelectRepository $relationFacadeSelectRepository
     * @param RelationTable                  $table
     * @param RelationManager                $crudManager
     */
    public function __construct(
        DefaultContainer $defaultContainer,
        RelationFacadeSelectRepository $relationFacadeSelectRepository,
        RelationTable $table,
        RelationManager $crudManager
    ) {
        parent::__construct($defaultContainer, $table, $crudManager);

        $this->relationFacadeSelectRepository = $relationFacadeSelectRepository;
    }

    public function __destruct()
    {
        $this->relationFacadeSelectRepository = null;

        parent::__destruct();
    }

    /**
     * @return RelationFacadeSelectRepository
     */
    public function select()
    {
        return $this->relationFacadeSelectRepository;
    }
}
