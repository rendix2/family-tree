<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownAddCountryModalFactory.php
 * User: Tomáš Babický
 * Date: 23.03.2021
 * Time: 15:19
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Town\Factory;


use Rendix2\FamilyTree\App\Controls\Modals\Town\TownAddCountryModal;

interface TownAddCountryModalFactory
{
    /**
     * @return TownAddCountryModal
     */
    public function create();
}