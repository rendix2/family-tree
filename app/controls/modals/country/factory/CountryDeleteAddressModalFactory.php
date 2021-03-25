<?php
/**
 *
 * Created by PhpStorm.
 * Filename: CountryAddresdDeleteModal.php
 * User: Tomáš Babický
 * Date: 30.10.2020
 * Time: 1:14
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Country\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Country\CountryDeleteAddressModal;

/**
 * Interface CountryDeleteAddressModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Country\Factory
 */
interface CountryDeleteAddressModalFactory
{
    /**
     * @return CountryDeleteAddressModal
     */
    public function create();
}
