<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownManager.php
 * User: Tomáš Babický
 * Date: 02.04.2021
 * Time: 15:13
 */

namespace Rendix2\FamilyTree\App\Model\Managers;

use Rendix2\FamilyTree\App\Filters\TownFilter;
use Rendix2\FamilyTree\App\Model\CrudManager\CrudManager;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultContainer;
use Rendix2\FamilyTree\App\Model\Managers\Town\TownSelectRepository;
use Rendix2\FamilyTree\App\Model\Managers\Town\TownTable;

/**
 * Class TownManager
 *
 * @package Rendix2\FamilyTree\App\Model\Managers
 */
class TownManager extends CrudManager
{
    /**
     * @var TownSelectRepository $townSelectRepository
     */
    private $townSelectRepository;

    /**
     * TownManager constructor.
     *
     * @param DefaultContainer     $defaultContainer
     * @param TownFilter           $townFilter
     * @param TownSelectRepository $townSelectRepository
     * @param TownTable            $townTable
     */
    public function __construct(
        DefaultContainer $defaultContainer,
        TownFilter $townFilter,
        TownSelectRepository $townSelectRepository,
        TownTable $townTable
    ) {
        parent::__construct($defaultContainer, $townTable, $townFilter);

        $this->townSelectRepository = $townSelectRepository;
    }

    /**
     * @return TownSelectRepository
     */
    public function select()
    {
        return $this->townSelectRepository;
    }
}
