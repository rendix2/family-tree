<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteWeddingModalFactory.php
 * User: Tomáš Babický
 * Date: 23.03.2021
 * Time: 14:43
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Address\Factory;


use Rendix2\FamilyTree\App\Controls\Modals\Address\AddressDeleteWeddingModal;

interface AddressDeleteWeddingModalFactory
{
    /**
     * @return AddressDeleteWeddingModal
     */
    public function create();
}