<?php
/**
 *
 * Created by PhpStorm.
 * Filename: CountryDeleteTownModal.php
 * User: Tomáš Babický
 * Date: 30.10.2020
 * Time: 0:50
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Country\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Country\CountryDeleteTownModal;

/**
 * Interface CountryDeleteTownModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Country\Factory
 */
interface CountryDeleteTownModalFactory
{
    /**
     * @return CountryDeleteTownModal
     */
    public function create();
}
