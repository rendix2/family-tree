<?php
/**
 *
 * Created by PhpStorm.
 * Filename: NoteHistory.php
 * User: Tomáš Babický
 * Date: 16.09.2020
 * Time: 1:34
 */

namespace Rendix2\FamilyTree\App\Managers;

use Dibi\Fluent;

/**
 * Class NoteHistory
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class NoteHistoryManager extends CrudManager
{
    /**
     * @return Fluent
     */
    public function getAllJoinedPerson()
    {
        return $this->dibi
            ->select('n.*')
            ->select('p.name')
            ->select('p.surname')
            ->select('p.hasBirthDate')
            ->select('p.birthDate')
            ->select('p.hasBirthYear')
            ->select('p.birthYear')
            ->select('p.hasDeathDate')
            ->select('p.deathDate')
            ->select('p.hasDeathYear')
            ->select('p.deathYear')
            ->select('p.stillAlive')

            ->from($this->getTableName())
            ->as($this->getTableAlias())
            ->innerJoin(Tables::PERSON_TABLE)
            ->as('p')
            ->on('[n.personId] = [p.id]');
    }

    /**
     * @param int $personId
     * @return array
     */
    public function getByPerson($personId)
    {
        return $this->getAllFluent()
            ->where('[personId] = %i', $personId)
            ->fetchAll();
    }
}
