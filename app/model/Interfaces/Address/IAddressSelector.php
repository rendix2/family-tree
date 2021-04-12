<?php
/**
 *
 * Created by PhpStorm.
 * Filename: IAddressSelector.php
 * User: Tomáš Babický
 * Date: 03.04.2021
 * Time: 23:31
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Address\Interfaces;

use Rendix2\FamilyTree\App\Model\Entities\AddressEntity;
use Rendix2\FamilyTree\App\Model\Interfaces\ISelector;

/**
 * Interface IAddressSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Interfaces
 */
interface IAddressSelector extends ISelector
{
    /**
     * @param int $countryId
     *
     * @return AddressEntity[]
     */
    public function getByCountryId($countryId);

    /**
     * @param int $townId
     *
     * @return AddressEntity[]
     */
    public function getByTownId($townId);

    /**
     * @return AddressEntity[]
     */
    public function getToMap();

    /**
     * @param int $townId
     *
     * @return AddressEntity[]
     */
    public function getByTownPairs($townId);
}
