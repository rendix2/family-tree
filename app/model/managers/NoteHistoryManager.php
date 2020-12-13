<?php
/**
 *
 * Created by PhpStorm.
 * Filename: HistoryNote.php
 * User: Tomáš Babický
 * Date: 16.09.2020
 * Time: 1:34
 */

namespace Rendix2\FamilyTree\App\Managers;

use Dibi\Fluent;
use Dibi\Row;
use Rendix2\FamilyTree\App\Model\Entities\HistoryNoteEntity;

/**
 * Class HistoryNote
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class NoteHistoryManager extends CrudManager
{
    /**
     * @return HistoryNoteEntity[]
     */
    public function getAll()
    {
        return $this->getAllFluent()
            ->execute()
            ->setRowClass(HistoryNoteEntity::class)
            ->fetchAll();
    }

    /**
     * @param int $id
     *
     * @return HistoryNoteEntity|false
     */
    public function getByPrimaryKey($id)
    {
        return $this->getAllFluent()
            ->where('%n = %i', $this->getPrimaryKey(), $id)
            ->execute()
            ->setRowClass(HistoryNoteEntity::class)
            ->fetch();
    }

    /**
     * @param Fluent $query
     *
     * @return HistoryNoteEntity[]
     */
    public function getBySubQuery(Fluent $query)
    {
        return $this->getAllFluent()
            ->where('%n in %sql', $this->getPrimaryKey(), $query)
            ->execute()
            ->setRowClass(HistoryNoteEntity::class)
            ->fetchAll();
    }

    /**
     * @param int $personId
     *
     * @return HistoryNoteEntity[]
     */
    public function getByPerson($personId)
    {
        return $this->getAllFluent()
            ->where('[personId] = %i', $personId)
            ->execute()
            ->setRowClass(HistoryNoteEntity::class)
            ->fetchAll();
    }
}
