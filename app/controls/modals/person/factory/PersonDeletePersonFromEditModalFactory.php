<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeletePersonFromEditModalFactory.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 12:20
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person\Factory;


use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonDeletePersonFromEditModal;

/**
 * Interface PersonDeletePersonFromEditModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person\Factory
 */
interface PersonDeletePersonFromEditModalFactory
{
    /**
     * @return PersonDeletePersonFromEditModal
     */
    public function create();
}
