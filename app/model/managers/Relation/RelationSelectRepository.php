<?php
/**
 *
 * Created by PhpStorm.
 * Filename: RelationSelectRepository.php
 * User: Tomáš Babický
 * Date: 06.04.2021
 * Time: 21:47
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Relation;


use Dibi\Connection;
use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Filters\RelationFilter;
use Rendix2\FamilyTree\App\Model\Interfaces\ISelectRepository;

/**
 * Class RelationSelectRepository
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Relation
 */
class RelationSelectRepository implements ISelectRepository
{
    /**
     * @var RelationCachedSelector $relationCachedSelector
     */
    private $relationCachedSelector;

    /**
     * @var RelationSelector $relationSelector
     */
    private $relationSelector;

    /**
     * RelationSelectRepository constructor.
     *
     * @param Connection             $connection
     * @param IStorage               $storage
     * @param RelationTable          $table
     * @param RelationFilter         $filter
     * @param RelationSelector       $relationSelector
     * @param RelationCachedSelector $relationCachedSelector
     */
    public function __construct(
        RelationSelector $relationSelector,
        RelationCachedSelector $relationCachedSelector
    ) {
        $this->relationSelector = $relationSelector;
        $this->relationCachedSelector = $relationCachedSelector;
    }

    public function __destruct()
    {
        $this->relationSelector = null;
        $this->relationCachedSelector = null;
    }

    /**
     * @return RelationSelector
     */
    public function getManager()
    {
        return $this->relationSelector;
    }

    /**
     * @return RelationCachedSelector
     */
    public function getCachedManager()
    {
        return $this->relationCachedSelector;
    }
}
