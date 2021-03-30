<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownDeleteTownJobModalFactory.php
 * User: Tomáš Babický
 * Date: 23.03.2021
 * Time: 15:22
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Town\Factory;


use Rendix2\FamilyTree\App\Controls\Modals\Town\TownDeleteTownJobModal;

interface TownDeleteTownJobModalFactory
{
    /**
     * @return TownDeleteTownJobModal
     */
    public function create();
}