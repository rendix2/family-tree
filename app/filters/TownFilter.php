<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownFilter.php
 * User: Tomáš Babický
 * Date: 24.09.2020
 * Time: 1:44
 */

namespace Rendix2\FamilyTree\App\Filters;

use Rendix2\FamilyTree\App\Model\Entities\TownEntity;

/**
 * Class TownFilter
 *
 * @package Rendix2\FamilyTree\App\Filters
 */
class TownFilter implements IFilter
{
    /**
     * @param TownEntity $town
     * @return string
     */
    public function __invoke(TownEntity $town = null)
    {
        if ($town === null) {
            return '';
        }

        return $town->name . ' ' . $town->zipCode;
    }
}
