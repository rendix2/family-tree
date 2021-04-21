<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceFacade.php
 * User: Tomáš Babický
 * Date: 12.11.2020
 * Time: 5:11
 */

namespace Rendix2\FamilyTree\App\Model\Facades;

use Rendix2\FamilyTree\App\Model\CrudManager\DefaultContainer;
use Rendix2\FamilyTree\App\Model\Facades\DefaultFacade\DefaultFacade;
use Rendix2\FamilyTree\App\Model\Facades\Source\SourceFacadeSelectRepository;
use Rendix2\FamilyTree\App\Model\Managers\SourceManager;
use Rendix2\FamilyTree\App\Model\Table\SourceTable;

/**
 * Class SourceFacade
 *
 * @package Rendix2\FamilyTree\App\Model\Facades
 */
class SourceFacade extends DefaultFacade
{
    /**
     * @var SourceFacadeSelectRepository $sourceFacadeSelectRepository
     */
    private $sourceFacadeSelectRepository;

    /**
     * SourceFacade constructor.
     *
     * @param DefaultContainer             $defaultContainer
     * @param SourceTable                  $table
     * @param SourceManager                $crudManager
     * @param SourceFacadeSelectRepository $sourceFacadeSelectRepository
     */
    public function __construct(
        DefaultContainer $defaultContainer,
        SourceTable $table,
        SourceManager $crudManager,
        SourceFacadeSelectRepository $sourceFacadeSelectRepository
    ) {
        parent::__construct($defaultContainer, $table, $crudManager);

        $this->sourceFacadeSelectRepository = $sourceFacadeSelectRepository;
    }

    public function __destruct()
    {
        $this->sourceFacadeSelectRepository = null;

        parent::__destruct();
    }

    /**
     * @return SourceFacadeSelectRepository
     */
    public function select()
    {
        return $this->sourceFacadeSelectRepository;
    }
}
