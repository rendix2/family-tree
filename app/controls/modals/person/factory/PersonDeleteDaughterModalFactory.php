<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteDaughterModalFactory.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:08
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonDeleteDaughterModal;

/**
 * Interface PersonDeleteDaughterModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person\Factory
 */
interface PersonDeleteDaughterModalFactory
{
    /**
     * @return PersonDeleteDaughterModal
     */
    public function create();
}
