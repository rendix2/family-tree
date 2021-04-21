<?php
/**
 *
 * Created by PhpStorm.
 * Filename: HistoryNoteManager.php
 * User: Tomáš Babický
 * Date: 02.04.2021
 * Time: 15:11
 */

namespace Rendix2\FamilyTree\App\Model\Managers;

use Rendix2\FamilyTree\App\Filters\HistoryNoteFilter;
use Rendix2\FamilyTree\App\Model\CrudManager\CrudManager;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultContainer;
use Rendix2\FamilyTree\App\Model\Managers\HistoryNote\HistoryNoteSelectRepository;
use Rendix2\FamilyTree\App\Model\Managers\HistoryNote\HistoryNoteTable;

/**
 * Class HistoryNoteManager
 *
 * @package Rendix2\FamilyTree\App\Model\Managers
 */
class HistoryNoteManager extends CrudManager
{
    /**
     * @var HistoryNoteSelectRepository $historyNoteSelectRepository
     */
    private $historyNoteSelectRepository;

    /**
     * HistoryNoteManager constructor.
     *
     * @param DefaultContainer            $defaultContainer
     * @param HistoryNoteFilter           $historyNoteFilter
     * @param HistoryNoteTable            $table
     * @param HistoryNoteSelectRepository $historyNoteSelectRepository
     */
    public function __construct(
        DefaultContainer $defaultContainer,
        HistoryNoteFilter $historyNoteFilter,
        HistoryNoteTable $table,
        HistoryNoteSelectRepository $historyNoteSelectRepository
    ) {
        parent::__construct($defaultContainer, $table, $historyNoteFilter);

        $this->historyNoteSelectRepository = $historyNoteSelectRepository;
    }

    public function __destruct()
    {
        $this->historyNoteSelectRepository = null;

        parent::__destruct();
    }

    /**
     * @return HistoryNoteSelectRepository
     */
    public function select()
    {
        return $this->historyNoteSelectRepository;
    }
}
