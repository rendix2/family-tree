<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteAddressJobModalFactory.php
 * User: Tomáš Babický
 * Date: 23.03.2021
 * Time: 14:41
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Address\Factory;


use Rendix2\FamilyTree\App\Controls\Modals\Address\AddressDeleteAddressJobModal;

interface AddressDeleteAddressJobModalFactory
{
    /**
     * @return AddressDeleteAddressJobModal
     */
    public function create();
}