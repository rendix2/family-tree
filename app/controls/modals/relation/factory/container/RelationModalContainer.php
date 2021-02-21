<?php
/**
 *
 * Created by PhpStorm.
 * Filename: RelationModalContainer.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 3:03
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Relation\Container;

use Rendix2\FamilyTree\App\Controls\Modals\Relation\Factory\RelationDeleteRelationFromEditModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Relation\Factory\RelationDeleteRelationFromListModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Relation\RelationDeleteRelationFromEditModal;
use Rendix2\FamilyTree\App\Controls\Modals\Relation\RelationDeleteRelationFromListModal;

/**
 * Class RelationModalContainer
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Relation\Container
 */
class RelationModalContainer
{
    /**
     * @var RelationDeleteRelationFromEditModalFactory $relationDeleteRelationFromEditModalFactory
     */
    private $relationDeleteRelationFromEditModalFactory;

    /**
     * @var RelationDeleteRelationFromListModalFactory $relationDeleteRelationFromListModalFactory
     */
    private $relationDeleteRelationFromListModalFactory;

    /**
     * RelationModalContainer constructor.
     * @param RelationDeleteRelationFromEditModalFactory $relationDeleteRelationFromEditModalFactory
     * @param RelationDeleteRelationFromListModalFactory $relationDeleteRelationFromListModalFactory
     */
    public function __construct(
        RelationDeleteRelationFromEditModalFactory $relationDeleteRelationFromEditModalFactory,
        RelationDeleteRelationFromListModalFactory $relationDeleteRelationFromListModalFactory
    ) {
        $this->relationDeleteRelationFromEditModalFactory = $relationDeleteRelationFromEditModalFactory;
        $this->relationDeleteRelationFromListModalFactory = $relationDeleteRelationFromListModalFactory;
    }

    /**
     * @return RelationDeleteRelationFromEditModalFactory
     */
    public function getRelationDeleteRelationFromEditModalFactory()
    {
        return $this->relationDeleteRelationFromEditModalFactory;
    }

    /**
     * @return RelationDeleteRelationFromListModalFactory
     */
    public function getRelationDeleteRelationFromListModalFactory()
    {
        return $this->relationDeleteRelationFromListModalFactory;
    }
}
