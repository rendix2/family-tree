<?php
/**
 *
 * Created by PhpStorm.
 * Filename: NameFacade.php
 * User: Tomáš Babický
 * Date: 11.11.2020
 * Time: 17:57
 */

namespace Rendix2\FamilyTree\App\Model\Facades;

use Rendix2\FamilyTree\App\Model\CrudManager\DefaultContainer;
use Rendix2\FamilyTree\App\Model\Facades\DefaultFacade\DefaultFacade;
use Rendix2\FamilyTree\App\Model\Facades\Name\NameFacadeSelectRepository;
use Rendix2\FamilyTree\App\Model\Managers\NameManager;
use Rendix2\FamilyTree\App\Model\Tables\NameTable;

/**
 * Class NameFacade
 *
 * @package Rendix2\FamilyTree\App\Model\Facades
 */
class NameFacade extends DefaultFacade
{
    /**
     * @var NameFacadeSelectRepository $nameFacadeSelectRepository
     */
    private $nameFacadeSelectRepository;

    /**
     * NameFacade constructor.
     *
     * @param DefaultContainer           $defaultContainer
     * @param NameFacadeSelectRepository $nameFacadeSelectRepository
     * @param NameTable                  $table
     * @param NameManager                $crudManager
     */
    public function __construct(
        DefaultContainer $defaultContainer,
        NameFacadeSelectRepository $nameFacadeSelectRepository,
        NameTable $table,
        NameManager $crudManager
    ) {
        parent::__construct($defaultContainer, $table, $crudManager);

        $this->nameFacadeSelectRepository = $nameFacadeSelectRepository;
    }
    public function __destruct()
    {
        $this->nameFacadeSelectRepository = null;

        parent::__destruct();
    }

    /**
     * @return NameFacadeSelectRepository
     */
    public function select()
    {
        return $this->nameFacadeSelectRepository;
    }
}
