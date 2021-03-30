<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownDeletePersonDeathModalFactory.php
 * User: Tomáš Babický
 * Date: 23.03.2021
 * Time: 15:21
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Town\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Town\TownDeleteDeathPersonModal;

/**
 * Interface TownDeleteDeathPersonModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Town\Factory
 */
interface TownDeleteDeathPersonModalFactory
{
    /**
     * @return TownDeleteDeathPersonModal
     */
    public function create();
}
