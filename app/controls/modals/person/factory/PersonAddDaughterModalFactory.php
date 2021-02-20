<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddDaughterModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 12:54
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonAddDaughterModal;

/**
 * Interface PersonAddDaughterModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person\Factory
 */
interface PersonAddDaughterModalFactory
{
    /**
     * @return PersonAddDaughterModal
     */
    public function create();
}