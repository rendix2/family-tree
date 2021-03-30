<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteDeathPersonModalFactory.php
 * User: Tomáš Babický
 * Date: 23.03.2021
 * Time: 14:41
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Address\Factory;


use Rendix2\FamilyTree\App\Controls\Modals\Address\AddressDeleteDeathPersonModal;

interface AddressDeleteDeathPersonModalFactory
{
    /**
     * @return AddressDeleteDeathPersonModal
     */
    public function create();
}