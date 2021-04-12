<?php
/**
 *
 * Created by PhpStorm.
 * Filename: WeddigFacade.php
 * User: Tomáš Babický
 * Date: 10.11.2020
 * Time: 2:44
 */

namespace Rendix2\FamilyTree\App\Model\Facades;

use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultContainer;
use Rendix2\FamilyTree\App\Model\Facades\DefaultFacade\DefaultFacade;
use Rendix2\FamilyTree\App\Model\Facades\Wedding\WeddingFacadeSelectRepository;
use Rendix2\FamilyTree\App\Model\Interfaces\ICrud;
use Rendix2\FamilyTree\App\Model\Interfaces\ITable;
use Rendix2\FamilyTree\App\Model\Managers\PersonManager;
use Rendix2\FamilyTree\App\Model\Entities\AddressEntity;
use Rendix2\FamilyTree\App\Model\Entities\DurationEntity;
use Rendix2\FamilyTree\App\Model\Entities\PersonEntity;
use Rendix2\FamilyTree\App\Model\Entities\TownEntity;
use Rendix2\FamilyTree\App\Model\Entities\WeddingEntity;
use Rendix2\FamilyTree\App\Model\Managers\Wedding\WeddingTable;
use Rendix2\FamilyTree\App\Model\Managers\WeddingManager;

/**
 * Class WeddingFacade
 *
 * @package Rendix2\FamilyTree\App\Facades
 */
class WeddingFacade extends DefaultFacade
{
    /**
     * @var WeddingFacadeSelectRepository $weddingFacadeSelectRepository
     */
    private $weddingFacadeSelectRepository;

    /**
     * WeddingFacade constructor.
     *
     * @param DefaultContainer              $defaultContainer
     * @param WeddingTable                  $table
     * @param WeddingManager                $crudManager
     * @param WeddingFacadeSelectRepository $weddingFacadeSelectRepository
     */
    public function __construct(
        DefaultContainer $defaultContainer,
        WeddingTable $table,
        WeddingManager $crudManager,
        WeddingFacadeSelectRepository $weddingFacadeSelectRepository
    ) {
        parent::__construct($defaultContainer, $table, $crudManager);

        $this->weddingFacadeSelectRepository = $weddingFacadeSelectRepository;
    }

    /**
     * @return WeddingFacadeSelectRepository
     */
    public function select()
    {
        return $this->weddingFacadeSelectRepository;
    }
}
