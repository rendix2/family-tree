<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressAddWeddingModalFactory.php
 * User: Tomáš Babický
 * Date: 23.03.2021
 * Time: 14:40
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Address\Factory;


use Rendix2\FamilyTree\App\Controls\Modals\Address\AddressAddWeddingModal;

interface AddressAddWeddingModalFactory
{
    /**
     * @return AddressAddWeddingModal
     */
    public function create();
}