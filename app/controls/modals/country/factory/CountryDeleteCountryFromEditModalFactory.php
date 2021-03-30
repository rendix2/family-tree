<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteAddressEditModal.php
 * User: Tomáš Babický
 * Date: 16.11.2020
 * Time: 21:12
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Country\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Country\CountryDeleteCountryFromEditModal;

/**
 * Interface CountryDeleteCountryFromEditModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Country\Factory
 */
interface CountryDeleteCountryFromEditModalFactory
{
    /**
     * @return CountryDeleteCountryFromEditModal
     */
    public function create();
}
