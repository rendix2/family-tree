<?php
/**
 *
 * Created by PhpStorm.
 * Filename: RelationDeleteRelationFromEditModalFactory.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 2:02
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Relation\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Relation\RelationDeleteRelationFromEditModal;

/**
 * Interface RelationDeleteRelationFromEditModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Relation\Factory
 */
interface RelationDeleteRelationFromEditModalFactory
{
    /**
     * @return RelationDeleteRelationFromEditModal
     */
    public function create();
}
