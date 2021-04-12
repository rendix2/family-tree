<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceTypeFilter.php
 * User: Tomáš Babický
 * Date: 24.11.2020
 * Time: 16:25
 */

namespace Rendix2\FamilyTree\App\Filters;

use Rendix2\FamilyTree\App\Model\Entities\SourceTypeEntity;

/**
 * Class SourceTypeFilter
 *
 * @package Rendix2\FamilyTree\App\Filters
 */
class SourceTypeFilter implements IFilter
{
    /**
     * @param SourceTypeEntity $sourceTypeEntity
     *
     * @return string
     */
    public function __invoke(SourceTypeEntity $sourceTypeEntity)
    {
        return $sourceTypeEntity->name;
    }
}
