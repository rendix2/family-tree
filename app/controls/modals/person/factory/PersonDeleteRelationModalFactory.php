<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteRelationModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:14
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonDeleteRelationModal;

/**
 * Interface PersonDeleteRelationModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person\Factory
 */
interface PersonDeleteRelationModalFactory
{
    /**
     * @return PersonDeleteRelationModal
     */
    public function create();
}
