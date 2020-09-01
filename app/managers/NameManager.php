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
     * @param int $peopleId
     *
     * @return Row[]
     */
    public function getByPeopleId($peopleId)
    {
        return $this->getAllFluent()
            ->where('[peopleId] = %i', $peopleId)
            ->orderBy('dateSince', \dibi::ASC)
            ->fetchAll();
    }

    /**
     * @param int $peopleId
     *
     * @return Result|int
     */
    public function deleteByPeopleId($peopleId)
    {
        return $this->deleteFluent()
            ->where('[peopleId] = %i', $peopleId)
            ->execute();
    }
}
