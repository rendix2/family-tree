<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteBrotherModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:08
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonDeleteBrotherModal;

/**
 * Interface PersonDeleteBrotherModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person\Factory
 */
interface PersonDeleteBrotherModalFactory
{
    /**
     * @return PersonDeleteBrotherModal
     */
    public function create();
}
