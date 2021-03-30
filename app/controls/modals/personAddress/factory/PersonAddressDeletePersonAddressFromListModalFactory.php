<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddressDeletePersonAddressFromListModalFactory.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 2:01
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\PersonAddress\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\PersonAddress\PersonAddressDeletePersonAddressFromListModal;

/**
 * Interface PersonAddressDeletePersonAddressFromListModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\PersonAddress\Factory
 */
interface PersonAddressDeletePersonAddressFromListModalFactory
{
    /**
     * @return PersonAddressDeletePersonAddressFromListModal
     */
    public function create();
}
