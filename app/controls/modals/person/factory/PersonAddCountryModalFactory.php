<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddCountryModalFactory.php
 * User: Tomáš Babický
 * Date: 30.03.2021
 * Time: 10:22
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonAddCountryModal;

/**
 * Interface PersonAddCountryModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person\Factory
 */
interface PersonAddCountryModalFactory
{
    /**
     * @return PersonAddCountryModal
     */
    public function create();
}