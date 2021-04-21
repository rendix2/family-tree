<?php
/**
 *
 * Created by PhpStorm.
 * Filename: WeddingManager.php
 * User: Tomáš Babický
 * Date: 02.04.2021
 * Time: 15:17
 */

namespace Rendix2\FamilyTree\App\Model\Managers;

use Rendix2\FamilyTree\App\Filters\WeddingFilter;
use Rendix2\FamilyTree\App\Model\CrudManager\CrudManager;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultContainer;
use Rendix2\FamilyTree\App\Model\Managers\Wedding\WeddingDeleter;
use Rendix2\FamilyTree\App\Model\Managers\Wedding\WeddingSelectRepository;
use Rendix2\FamilyTree\App\Model\Managers\Wedding\WeddingTable;

/**
 * Class WeddingManager
 *
 * @package Rendix2\FamilyTree\App\Model\Managers
 */
class WeddingManager extends CrudManager
{
    /**
     * @var WeddingDeleter $weddingDeleter
     */
    private $weddingDeleter;

    /**
     * @var WeddingSelectRepository $weddingSelectRepository
     */
    private $weddingSelectRepository;

    /**
     * WeddingManager constructor.
     *
     * @param DefaultContainer        $defaultContainer
     * @param WeddingSelectRepository $weddingSelectRepository
     * @param WeddingDeleter          $weddingDeleter
     * @param WeddingFilter           $weddingFilter
     * @param WeddingTable            $table
     */
    public function __construct(
        DefaultContainer $defaultContainer,
        WeddingSelectRepository $weddingSelectRepository,
        WeddingDeleter $weddingDeleter,
        WeddingFilter $weddingFilter,
        WeddingTable $table
    ) {
        parent::__construct($defaultContainer, $table, $weddingFilter);

        $this->weddingDeleter = $weddingDeleter;
        $this->weddingSelectRepository = $weddingSelectRepository;
    }

    public function __destruct()
    {
        $this->weddingDeleter = null;
        $this->weddingSelectRepository = null;

        parent::__destruct();
    }

    /**
     * @return WeddingSelectRepository
     */
    public function select()
    {
        return $this->weddingSelectRepository;
    }

    /**
     * @return WeddingDeleter
     */
    public function delete()
    {
        return $this->weddingDeleter;
    }
}
