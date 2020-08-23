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

use Dibi\Exception;
use Dibi\Result;

/**
 * Class PeopleManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class PeopleManager extends CrudManager
{
    /**
     * @param int $motherId
     *
     * @return array
     */
    public function getByMotherId($motherId)
    {
        return $this->getAllFluent()
            ->where('[mother_id] = %i', $motherId)
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
            ->where('[father_id] = %i', $fatherId)
            ->fetchAll();
    }

    /**
     * @param int $motherId
     *
     * @return Result|int
     * @throws Exception
     */
    public function deleteByMotherId($motherId)
    {
        return $this->getAllFluent()
            ->where('[mother_id] = %i', $motherId)
            ->execute();
    }

    /**
     * @param int $fatherId
     *
     * @return Result|int
     * @throws Exception
     */
    public function deleteByFatherId($fatherId)
    {
        return $this->getAllFluent()
            ->where('[father_id] = %i', $fatherId)
            ->execute();
    }
}
