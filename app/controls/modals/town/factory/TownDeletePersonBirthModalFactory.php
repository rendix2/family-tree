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


use Rendix2\FamilyTree\App\Controls\Modals\Town\TownDeletePersonBirthModal;

interface TownDeletePersonBirthModalFactory
{
    /**
     * @return TownDeletePersonBirthModal
     */
    public function create();
}