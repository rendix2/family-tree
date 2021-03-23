<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressAddJobModalFactory.php
 * User: Tomáš Babický
 * Date: 23.03.2021
 * Time: 14:39
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Address\Factory;


use Rendix2\FamilyTree\App\Controls\Modals\Address\AddressAddJobModal;

interface AddressAddJobModalFactory
{

    /**
     * @return AddressAddJobModal
     */
    public function create();
}