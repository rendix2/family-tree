<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteBirthPersonModalFactory.php
 * User: Tomáš Babický
 * Date: 23.03.2021
 * Time: 14:41
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Address\Factory;


use Rendix2\FamilyTree\App\Controls\Modals\Address\AddressDeleteBirthPersonModal;

interface AddressDeleteBirthPersonModalFactory
{
    /**
     * @return AddressDeleteBirthPersonModal
     */
    public function create();
}