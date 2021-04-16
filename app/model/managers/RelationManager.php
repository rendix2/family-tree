<?php
/**
 *
 * Created by PhpStorm.
 * Filename: RelationManager.php
 * User: Tomáš Babický
 * Date: 02.04.2021
 * Time: 15:13
 */

namespace Rendix2\FamilyTree\App\Model\Managers;

use Rendix2\FamilyTree\App\Filters\RelationFilter;
use Rendix2\FamilyTree\App\Model\CrudManager\CrudManager;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultContainer;
use Rendix2\FamilyTree\App\Model\Managers\Relation\RelationDeleter;
use Rendix2\FamilyTree\App\Model\Managers\Relation\RelationSelectRepository;
use Rendix2\FamilyTree\App\Model\Managers\Relation\RelationTable;

class RelationManager extends CrudManager
{
    /**
     * @var RelationDeleter $relationDeleter
     */
    private $relationDeleter;

    /**
     * @var RelationSelectRepository $relationSelectRepository
     */
    private $relationSelectRepository;

    /**
     * RelationManager constructor.
     *
     * @param DefaultContainer         $defaultContainer
     * @param RelationSelectRepository $relationSelectRepository
     * @param RelationDeleter          $relationDeleter
     * @param RelationFilter           $relationFilter
     * @param RelationTable            $table
     */
    public function __construct(
        DefaultContainer $defaultContainer,
        RelationSelectRepository $relationSelectRepository,
        RelationDeleter $relationDeleter,
        RelationFilter $relationFilter,
        RelationTable $table
    ) {
        parent::__construct($defaultContainer, $table, $relationFilter);

        $this->relationDeleter = $relationDeleter;
        $this->relationSelectRepository = $relationSelectRepository;
    }

    /**
     * @return RelationSelectRepository
     */
    public function select()
    {
        return $this->relationSelectRepository;
    }

    /**
     * @return RelationDeleter
     */
    public function delete()
    {
        return $this->relationDeleter;
    }
}
