<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddBrotheModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 12:53
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonAddBrotherModal;

/**
 * Interface PersonAddBrotherModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person\Factory
 */
interface PersonAddBrotherModalFactory
{
    /**
     * @return PersonAddBrotherModal
     */
   public function create();
}
