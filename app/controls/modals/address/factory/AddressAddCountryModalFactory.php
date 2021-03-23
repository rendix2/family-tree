<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressAddCountryModalFactory.php
 * User: Tomáš Babický
 * Date: 23.03.2021
 * Time: 14:36
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Address\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Address\AddressAddCountryModal;

interface AddressAddCountryModalFactory
{
    /**
     * @return AddressAddCountryModal
     */
    public function create();
}
