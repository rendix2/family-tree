<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownDeletePersonGravedModalFactory.php
 * User: Tomáš Babický
 * Date: 23.03.2021
 * Time: 15:21
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Town\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Town\TownDeleteGravedPersonModal;

/**
 * Interface TownDeleteGravedPersonModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Town\Factory
 */
interface TownDeleteGravedPersonModalFactory
{
    /**
     * @return TownDeleteGravedPersonModal
     */
    public function create();
}