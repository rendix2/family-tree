<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddSisterModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:05
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonAddSisterModal;

/**
 * Interface PersonAddSisterModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person\Factory
 */
interface PersonAddSisterModalFactory
{
    /**
     * @return PersonAddSisterModal
     */
    public function create();
}
