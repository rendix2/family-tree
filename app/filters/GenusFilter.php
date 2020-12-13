<?php
/**
 *
 * Created by PhpStorm.
 * Filename: GenusFilter.php
 * User: Tomáš Babický
 * Date: 24.09.2020
 * Time: 2:03
 */

namespace Rendix2\FamilyTree\App\Filters;

use Rendix2\FamilyTree\App\Model\Entities\GenusEntity;

/**
 * Class GenusFilter
 *
 * @package Rendix2\FamilyTree\App\Filters
 */
class GenusFilter
{
    /**
     * @param GenusEntity $genus
     *
     * @return string
     */
    public function __invoke(GenusEntity $genus)
    {
        return $genus->surname;
    }
}
