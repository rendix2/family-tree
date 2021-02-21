<?php
/**
 *
 * Created by PhpStorm.
 * Filename: RelationDeleteRelationFromListModalFatory.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 2:03
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Relation\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Relation\RelationDeleteRelationFromListModal;

/**
 * Interface RelationDeleteRelationFromListModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Relation\Factory
 */
interface RelationDeleteRelationFromListModalFactory
{
    /**
     * @return RelationDeleteRelationFromListModal
     */
    public function create();
}
