<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteAddressFromEditModalFactory.php
 * User: Tomáš Babický
 * Date: 23.03.2021
 * Time: 14:40
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Address\Factory;


use Rendix2\FamilyTree\App\Controls\Modals\Address\AddressDeleteAddressFromEditModal;

interface AddressDeleteAddressFromEditModalFactory
{
    /**
     * @return AddressDeleteAddressFromEditModal
     */
    public function create();
}