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

/**
 * Class TwinsManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class TwinsManager extends CrudManager
{
    /**
     * @param int $motherId
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
        return $this->deleteFluent()
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
        return $this->deleteFluent()
            ->where('[fatherId] = %i', $fatherId)
            ->execute();
    }
}