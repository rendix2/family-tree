<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownAddAddressModalFactory.php
 * User: Tomáš Babický
 * Date: 23.03.2021
 * Time: 15:19
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Town\Factory;


use Rendix2\FamilyTree\App\Controls\Modals\Town\TownAddAddressModal;

interface TownAddAddressModalFactory
{
    /**
     * @return TownAddAddressModal
     */
    public function create();
}