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

use dibi;
use Dibi\Result;

/**
 * Class PeopleManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class PeopleManager extends CrudManager
{
    public function get()
    {
        return $this
            ->dibi
            ->select('p.id')
            ->as('id')

            ->select('CONCAT(p.name, " ", p.surname)')
            ->as('name')

            ->select('p.sex')
            ->as('gender')

            ->select('p.fatherId')
            ->select('p.motherId')

            ->select('p.genusId')
            ->as('stpid')

            ->select('DATE_FORMAT(p.birthDate, "%d.%m.%Y")')
            ->as('birthDate')

            ->select('CONCAT(m.name, " ", m.surname)')
            ->as('motherName')

            ->select('CONCAT(f.name, " ", f.surname)')
            ->as('fatherName')

            ->from($this->getTableName())
            ->as('p')

            ->leftJoin($this->getTableName())
            ->as('m')
            ->on('[p.motherId] = [m.id]')

            ->leftJoin($this->getTableName())
            ->as('f')
            ->on('[p.fatherId] = [f.id]')

            ->orderBy('p.id', dibi::ASC)

            ->fetchAll();
    }

    /**
     * @param int $motherId
     *
     * @return array
     */
    public function getByMotherId($motherId)
    {
        return $this->getAllFluent()
            ->where('[motherId] = %i', $motherId)
            ->fetchAll();
    }

    /**
     * @param int $fatherId
     *
     * @return array
     */
    public function getByFatherId($fatherId)
    {
        return $this->getAllFluent()
            ->where('[fatherId] = %i', $fatherId)
            ->fetchAll();
    }

    /**
     * @param int $motherId
     *
     * @return Result|int
     */
    public function deleteByMotherId($motherId)
    {
        return $this->getAllFluent()
            ->where('[motherId] = %i', $motherId)
            ->execute();
    }

    /**
     * @param int $fatherId
     *
     * @return Result|int
     */
    public function deleteByFatherId($fatherId)
    {
        return $this->getAllFluent()
            ->where('[fatherId] = %i', $fatherId)
            ->execute();
    }

    public function getAllPairs()
    {
        return $this->dibi
            ->select('id')
            ->select('CONCAT(name, " ", surname)')
            ->as('name')
            ->from($this->getTableName())
            ->fetchPairs('id', 'name');
    }

    public function getMalesPairs()
    {
        return $this->dibi
            ->select('id')
            ->select('CONCAT(name, " ", surname)')
            ->as('name')
            ->from($this->getTableName())
            ->where('[sex] = %s', 'm')
            ->fetchPairs('id', 'name');
    }

    public function getFemalesPairs()
    {
        return $this->dibi
            ->select('id')
            ->select('CONCAT(name, " ", surname)')
            ->as('name')
            ->from($this->getTableName())
            ->where('[sex] = %s', 'f')
            ->fetchPairs('id', 'name');
    }

    /**
     * @param int $id
     * @return array
     */
    public function getChildrenByFather($id)
    {
        return $this->getAllFluent()
            ->where('[fatherId] = %i', $id)
            ->fetchAll();
    }

    /**
     * @param int $id
     * @return array
     */
    public function getChildrenByMother($id)
    {
        return $this->getAllFluent()
            ->where('[motherId] = %i', $id)
            ->fetchAll();
    }

}
