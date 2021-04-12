<?php
/**
 *
 * Created by PhpStorm.
 * Filename: RelationFilter.php
 * User: Tomáš Babický
 * Date: 23.11.2020
 * Time: 0:46
 */

namespace Rendix2\FamilyTree\App\Filters;

use Rendix2\FamilyTree\App\Model\Entities\RelationEntity;

/**
 * Class RelationFilter
 *
 * @package Rendix2\FamilyTree\App\Filters
 */
class RelationFilter implements IFilter
{
    /**
     * @var PersonFilter $personFilter
     */
    private $personFilter;

    /**
     * RelationFilter constructor.
     *
     * @param PersonFilter $personFilter
     */
    public function __construct(PersonFilter $personFilter)
    {
        $this->personFilter = $personFilter;
    }

    /**
     * @param RelationEntity $weddingEntity
     *
     * @return string
     */
    public function __invoke(RelationEntity $weddingEntity)
    {
        $personFilter = $this->personFilter;

        return $personFilter($weddingEntity->male) . ', ' . $personFilter($weddingEntity->female);
    }
}
