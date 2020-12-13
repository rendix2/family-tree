<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceFilter.php
 * User: Tomáš Babický
 * Date: 22.11.2020
 * Time: 2:13
 */

namespace Rendix2\FamilyTree\App\Filters;

use Rendix2\FamilyTree\App\Model\Entities\SourceEntity;

/**
 * Class SourceFilter
 *
 * @package Rendix2\FamilyTree\App\Filters
 */
class SourceFilter
{
    /**
     * @param SourceEntity $sourceEntity
     *
     * @return string
     */
    public function __invoke(SourceEntity $sourceEntity)
    {
        return $sourceEntity->link;
    }
}
