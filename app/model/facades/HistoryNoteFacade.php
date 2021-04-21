<?php
/**
 *
 * Created by PhpStorm.
 * Filename: HistoryNoteFacade.php
 * User: Tomáš Babický
 * Date: 16.11.2020
 * Time: 1:13
 */

namespace Rendix2\FamilyTree\App\Model\Facades;

use Rendix2\FamilyTree\App\Model\CrudManager\DefaultContainer;
use Rendix2\FamilyTree\App\Model\Facades\DefaultFacade\DefaultFacade;
use Rendix2\FamilyTree\App\Model\Facades\HistoryNote\HistoryNoteFacadeSelectRepository;
use Rendix2\FamilyTree\App\Model\Managers\HistoryNote\HistoryNoteTable;
use Rendix2\FamilyTree\App\Model\Managers\HistoryNoteManager;

/**
 * Class HistoryNoteFacade
 *
 * @package Rendix2\FamilyTree\App\Model\Facades
 */
class HistoryNoteFacade extends DefaultFacade
{
    /**
     * @var HistoryNoteFacadeSelectRepository $historyNoteFacadeSelectRepository
     */
    private $historyNoteFacadeSelectRepository;

    /**
     * HistoryNoteFacade constructor.
     *
     * @param DefaultContainer                  $defaultContainer
     * @param HistoryNoteFacadeSelectRepository $historyNoteFacadeSelectRepository
     * @param HistoryNoteTable                  $table
     * @param HistoryNoteManager                $crudManager
     */
    public function __construct(
        DefaultContainer $defaultContainer,
        HistoryNoteFacadeSelectRepository $historyNoteFacadeSelectRepository,
        HistoryNoteTable $table,
        HistoryNoteManager $crudManager
    ) {
        parent::__construct($defaultContainer, $table, $crudManager);

        $this->historyNoteFacadeSelectRepository = $historyNoteFacadeSelectRepository;
    }

    public function __destruct()
    {
        $this->historyNoteFacadeSelectRepository = null;

        parent::__destruct();
    }

    /**
     * @return HistoryNoteFacadeSelectRepository
     */
    public function select()
    {
        return $this->historyNoteFacadeSelectRepository;
    }
}
