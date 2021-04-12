<?php
/**
 *
 * Created by PhpStorm.
 * Filename: RelationSelectRepository.php
 * User: Tomáš Babický
 * Date: 10.04.2021
 * Time: 14:20
 */

namespace Rendix2\FamilyTree\App\Model\Facades\Relation;


use Rendix2\FamilyTree\App\Model\Interfaces\ISelectRepository;

/**
 * Class RelationFacadeSelectRepository
 *
 * @package Rendix2\FamilyTree\App\Model\Facades\Relation
 */
class RelationFacadeSelectRepository implements ISelectRepository
{
    /**
     * @var RelationFacadeCachedSelector $relationFacadeCachedSelector
     */
    private $relationFacadeCachedSelector;

    /**
     * @var RelationFacadeSelector $relationFacadeSelector
     */
    private $relationFacadeSelector;

    /**
     * RelationFacadeSelectRepository constructor.
     *
     * @param RelationFacadeCachedSelector $relationFacadeCachedSelector
     * @param RelationFacadeSelector       $relationFacadeSelector
     */
    public function __construct(
        RelationFacadeCachedSelector $relationFacadeCachedSelector,
        RelationFacadeSelector $relationFacadeSelector
    ) {
        $this->relationFacadeCachedSelector = $relationFacadeCachedSelector;
        $this->relationFacadeSelector = $relationFacadeSelector;
    }

    /**
     * @return RelationFacadeSelector
     */
    public function getManager()
    {
        return $this->relationFacadeSelector;
    }

    /**
     * @return RelationFacadeCachedSelector
     */
    public function getCachedManager()
    {
        return $this->relationFacadeCachedSelector;
    }
}