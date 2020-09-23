<?php
/**
 *
 * Created by PhpStorm.
 * Filename: s.php
 * User: Tomáš Babický
 * Date: 23.08.2020
 * Time: 15:11
 */

namespace Rendix2\FamilyTree\App\Managers;

use Dibi\Result;
use Dibi\Row;

/**
 * Class NameManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class NameManager extends CrudManager
{
    /**
     * @return Row[]
     */
    public function getAllJoinedPerson()
    {
        return $this->dibi
            ->select('CONCAT(p.name, " ", p.surname)')
            ->as('personName')
            ->select('n.*')
            ->from($this->getTableName())
            ->as('n')
            ->innerJoin(Tables::PERSON_TABLE)
            ->as('p')
            ->on('[n.personId] = [p.id]')
            ->fetchAll();
    }

    /**
     * @param int $personId
     *
     * @return Row[]
     */
    public function getByPersonId($personId)
    {
        return $this->getAllFluent()
            ->where('[personId] = %i', $personId)
            ->orderBy('dateSince', \dibi::ASC)
            ->fetchAll();
    }

    /**
     * @param int $genusId
     *
     * @return Row[]
     */
    public function getByGenusId($genusId)
    {
        return $this->getAllFluent()
            ->where('[genusId] = %i', $genusId)
            ->fetchAll();
    }

    /**
     * @param int $personId
     *
     * @return Result|int
     */
    public function deleteByPersonId($personId)
    {
        return $this->deleteFluent()
            ->where('[personId] = %i', $personId)
            ->execute();
    }
}
