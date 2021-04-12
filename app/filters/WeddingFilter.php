<?php
/**
 *
 * Created by PhpStorm.
 * Filename: WeddingFilter.php
 * User: Tomáš Babický
 * Date: 23.11.2020
 * Time: 0:46
 */

namespace Rendix2\FamilyTree\App\Filters;

use Rendix2\FamilyTree\App\Model\Entities\WeddingEntity;

/**
 * Class WeddingFilter
 *
 * @package Rendix2\FamilyTree\App\Filters
 */
class WeddingFilter implements IFilter
{
    /**
     * @var PersonFilter $personFilter
     */
    private $personFilter;

    /**
     * WeddingFilter constructor.
     *
     * @param PersonFilter $personFilter
     */
    public function __construct(PersonFilter $personFilter)
    {
        $this->personFilter = $personFilter;
    }

    /**
     * @param WeddingEntity $weddingEntity
     *
     * @return string
     */
    public function __invoke(WeddingEntity $weddingEntity)
    {
        $personFilter = $this->personFilter;

        return $personFilter($weddingEntity->husband) . ', ' . $personFilter($weddingEntity->wife);
    }
}
