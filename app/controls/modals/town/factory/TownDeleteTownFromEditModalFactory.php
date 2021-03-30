<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownDeleteTownFromEditModalFactory.php
 * User: Tomáš Babický
 * Date: 23.03.2021
 * Time: 15:22
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Town\Factory;


use Rendix2\FamilyTree\App\Controls\Modals\Town\TownDeleteTownFromEditModal;

interface TownDeleteTownFromEditModalFactory
{
    /**
     * @return TownDeleteTownFromEditModal
     */
    public function create();
}