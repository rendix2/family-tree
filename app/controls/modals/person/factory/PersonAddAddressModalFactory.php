<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddAddressModalFactory.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 12:18
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonAddAddressModal;

/**
 * Interface PersonAddAddressModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person\Factory
 */
interface PersonAddAddressModalFactory
{
    /**
     * @return PersonAddAddressModal
     */
    public function create();
}
