<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownFacade.php
 * User: Tomáš Babický
 * Date: 10.11.2020
 * Time: 2:51
 */

namespace Rendix2\FamilyTree\App\Model\Facades;

use Rendix2\FamilyTree\App\Model\CrudManager\DefaultContainer;
use Rendix2\FamilyTree\App\Model\Facades\DefaultFacade\DefaultFacade;
use Rendix2\FamilyTree\App\Model\Facades\Town\TownFacadeSelectRepository;
use Rendix2\FamilyTree\App\Model\Managers\Town\TownTable;
use Rendix2\FamilyTree\App\Model\Managers\TownManager;

/**
 * Class TownFacade
 *
 * @package Rendix2\FamilyTree\App\Facades
 */
class TownFacade extends DefaultFacade
{
    /**
     * @var TownFacadeSelectRepository $townFacadeSelectRepository
     */
    private $townFacadeSelectRepository;

    /**
     * TownFacade constructor.
     *
     * @param DefaultContainer           $defaultContainer
     * @param TownFacadeSelectRepository $townFacadeSelectRepository
     * @param TownTable                  $table
     * @param TownManager                $crudManager
     */
    public function __construct(
        DefaultContainer $defaultContainer,
        TownFacadeSelectRepository $townFacadeSelectRepository,
        TownTable $table,
        TownManager $crudManager
    ) {
        parent::__construct($defaultContainer, $table, $crudManager);

        $this->townFacadeSelectRepository = $townFacadeSelectRepository;
    }

    /**
     * @return TownFacadeSelectRepository
     */
    public function select()
    {
        return $this->townFacadeSelectRepository;
    }
}
