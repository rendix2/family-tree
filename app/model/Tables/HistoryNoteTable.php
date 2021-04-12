<?php
/**
 *
 * Created by PhpStorm.
 * Filename: HistoryNoteTable.php
 * User: Tomáš Babický
 * Date: 04.04.2021
 * Time: 23:27
 */

namespace Rendix2\FamilyTree\App\Model\Managers\HistoryNote;

use Rendix2\FamilyTree\App\Model\Entities\HistoryNoteEntity;
use Rendix2\FamilyTree\App\Model\Interfaces\ITable;
use Rendix2\FamilyTree\App\Model\Managers\Tables;

/**
 * Class HistoryNoteTable
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\HistoryNote
 */
class HistoryNoteTable implements ITable
{

    public function getTableName()
    {
        return Tables::HISTORY_NOTE_TABLE;
    }

    public function getEntity()
    {
        return HistoryNoteEntity::class;
    }

    public function getPrimaryKey()
    {
        return 'id';
    }
}
