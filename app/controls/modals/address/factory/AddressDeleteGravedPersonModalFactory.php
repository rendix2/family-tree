<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteGravedPersonModalFactory.php
 * User: Tomáš Babický
 * Date: 23.03.2021
 * Time: 14:42
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Address\Factory;


use Rendix2\FamilyTree\App\Controls\Modals\Address\AddressDeleteGravedPersonModal;

interface AddressDeleteGravedPersonModalFactory
{
    /**
     * @return AddressDeleteGravedPersonModal
     */
    public function create();
}