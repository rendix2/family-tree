<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteRelationParentModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:15
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonDeleteRelationParentModal;

/**
 * Interface PersonDeleteRelationParentModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person\Factory
 */
interface PersonDeleteRelationParentModalFactory
{
    /**
     * @return PersonDeleteRelationParentModal
     */
    public function create();
}
