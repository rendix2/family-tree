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

/**
 * Class JobManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class JobManager extends CrudManager
{
    /**
     * @param int $placeId
     * @return array
     */
    public function getByPlaceId($placeId)
    {
        return $this->getAllFluent()
            ->where('[placeId] = %i', $placeId)
            ->fetchAll();
    }

    /**
     * @param int $addressId
     * @return array
     */
    public function getByAddressId($addressId)
    {
        return $this->getAllFluent()
            ->where('[addressId] = %i', $addressId)
            ->fetchAll();
    }
}
