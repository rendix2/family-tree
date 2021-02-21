<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddressDeletePersonAddressFromEditModalFactory.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 2:01
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\PersonAddress\Factory;


use Rendix2\FamilyTree\App\Controls\Modals\PersonAddress\PersonAddressDeletePersonAddressFromEditModal;

interface PersonAddressDeletePersonAddressFromEditModalFactory
{
    /**
     * @return PersonAddressDeletePersonAddressFromEditModal
     */
    public function create();
}