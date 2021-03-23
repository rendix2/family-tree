<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownDeleteAddressModalFactory.php
 * User: Tomáš Babický
 * Date: 23.03.2021
 * Time: 15:20
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Town\Factory;


use Rendix2\FamilyTree\App\Controls\Modals\Town\TownDeleteAddressModal;

interface TownDeleteAddressModalFactory
{
    /**
     * @return TownDeleteAddressModal
     */
    public function create();
}