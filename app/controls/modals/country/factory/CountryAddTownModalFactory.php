<?php
/**
 *
 * Created by PhpStorm.
 * Filename: CountryAddTownModal.php
 * User: Tomáš Babický
 * Date: 02.12.2020
 * Time: 1:00
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Country\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Country\CountryAddTownModal;

/**
 * Interface CountryAddTownModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Country\Factory
 */
interface CountryAddTownModalFactory
{
    /**
     * @return CountryAddTownModal
     */
    public function create();
}
