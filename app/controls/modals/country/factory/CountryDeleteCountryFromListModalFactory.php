<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteAddressFromListModal.php
 * User: Tomáš Babický
 * Date: 16.11.2020
 * Time: 21:16
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Country\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Country\CountryDeleteCountryFromListModal;

/**
 * Interface CountryDeleteCountryFromListModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Country\Factory
 */
interface CountryDeleteCountryFromListModalFactory
{
    /**
     * @return CountryDeleteCountryFromListModal
     */
    public function create();
}
