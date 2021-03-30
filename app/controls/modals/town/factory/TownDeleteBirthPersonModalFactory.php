<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownDeletePersonBirthModalFactory.php
 * User: Tomáš Babický
 * Date: 23.03.2021
 * Time: 15:21
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Town\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Town\TownDeleteBirthPersonModal;

/**
 * Interface TownDeleteBirthPersonModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Town\Factory
 */
interface TownDeleteBirthPersonModalFactory
{
    /**
     * @return TownDeleteBirthPersonModal
     */
    public function create();
}
