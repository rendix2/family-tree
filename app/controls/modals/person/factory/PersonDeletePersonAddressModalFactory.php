<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeletePersonAddressModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:13
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonDeletePersonAddressModal;

/**
 * Interface PersonDeletePersonAddressModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person\Factory
 */
interface PersonDeletePersonAddressModalFactory
{
    /**
     * @return PersonDeletePersonAddressModal
     */
    public function create();
}
