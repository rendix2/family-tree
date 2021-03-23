<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownDeleteTownFromListModalFactory.php
 * User: Tomáš Babický
 * Date: 23.03.2021
 * Time: 15:22
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Town\Factory;


use Rendix2\FamilyTree\App\Controls\Modals\Town\TownDeleteTownFromListModal;

interface TownDeleteTownFromListModalFactory
{
    /**
     * @return TownDeleteTownFromListModal
     */
    public function create();
}