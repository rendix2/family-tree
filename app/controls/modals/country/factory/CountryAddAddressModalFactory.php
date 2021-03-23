<?php
/**
 *
 * Created by PhpStorm.
 * Filename: CountryAddAddressModalFactory.php
 * User: Tomáš Babický
 * Date: 02.12.2020
 * Time: 1:00
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Country\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Country\CountryAddAddressModal;

/**
 * Trait CountryAddAddressModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Country\Factory
 */
interface CountryAddAddressModalFactory
{
    /**
     * @return CountryAddAddressModal
     */
    public function create();
}