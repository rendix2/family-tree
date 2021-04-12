<?php
/**
 *
 * Created by PhpStorm.
 * Filename: HistoryNoteSelector.php
 * User: Tomáš Babický
 * Date: 04.04.2021
 * Time: 23:29
 */

namespace Rendix2\FamilyTree\App\Model\Managers\HistoryNote;

use Dibi\Connection;
use Rendix2\FamilyTree\App\Filters\HistoryNoteFilter;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultSelector;
use Rendix2\FamilyTree\App\Model\Entities\HistoryNoteEntity;
use Rendix2\FamilyTree\App\Model\Managers\HistoryNote\Interfaces\IHistoryNoteSelector;

/**
 * Class HistoryNoteSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\HistoryNote
 */
class HistoryNoteSelector extends DefaultSelector implements IHistoryNoteSelector
{
    public function __construct(
        Connection $connection,
        HistoryNoteTable $table,
        HistoryNoteFilter $historyNoteFilter
    ) {
        parent::__construct($connection, $table, $historyNoteFilter);
    }

    /**
     * @param $personId
     *
     * @return HistoryNoteEntity[]
     */
    public function getByPersonId($personId)
    {
        return $this->getAllFluent()
            ->where('[personId] = %i', $personId)
            ->execute()
            ->setRowClass($this->getTable()->getEntity())
            ->fetchAll();
    }
}