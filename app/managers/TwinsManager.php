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
 * Class TwinsManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class TwinsManager extends CrudManager
{
    /**
     * @param $motherId
     * @return array
     */
    public function getByMotherId($motherId)
    {
        return $this->getAllFluent()
            ->where('[mother_id] = %i', $motherId)
            ->fetchAll();
    }

    /**
     * @param int $motherId
     *
     * @return array
     */
    public function getByFatherId($motherId)
    {
        return $this->getAllFluent()
            ->where('[mother_id] = %i', $motherId)
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
        return $this->deleteFluent()
            ->where('[mother_id] = %i', $motherId)
            ->execute();
    }

    /**
     * @param int $motherId
     *
     * @return Result|int
     * @throws Exception
     */
    public function deleteByFatherId($motherId)
    {
        return $this->deleteFluent()
            ->where('[father_id] = %i', $motherId)
            ->execute();
    }
}
