<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddPersonAddressModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:02
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonAddPersonAddressModal;

/**
 * Interface PersonAddPersonAddressModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person\Factory
 */
interface PersonAddPersonAddressModalFactory
{
    /**
     * @return PersonAddPersonAddressModal
     */
    public function create();
}