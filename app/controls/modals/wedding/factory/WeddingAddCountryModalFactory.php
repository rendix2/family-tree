<?php
/**
 *
 * Created by PhpStorm.
 * Filename: WeddingAddCountryModalFactory.php
 * User: Tomáš Babický
 * Date: 30.03.2021
 * Time: 10:11
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Wedding\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Wedding\WeddingAddCountryModal;

/**
 * Interface WeddingAddCountryModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Wedding\Factory
 */
interface WeddingAddCountryModalFactory
{
    /**
     * @return WeddingAddCountryModal
     */
    public function create();
}
