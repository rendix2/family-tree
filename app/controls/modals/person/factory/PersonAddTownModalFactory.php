<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddTownModalFactory.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:06
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonAddTownModal;

/**
 * Interface PersonAddTownModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person\Factory
 */
interface PersonAddTownModalFactory
{
    /**
     * @return PersonAddTownModal
     */
    public function create();
}
