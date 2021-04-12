<?php
/**
 *
 * Created by PhpStorm.
 * Filename: IFilter.php
 * User: Tomáš Babický
 * Date: 14.02.2021
 * Time: 0:10
 */

namespace Rendix2\FamilyTree\App\Filters;

use Rendix2\FamilyTree\App\Model\Entities\IEntity;

/**
 * Interface IFilter
 *
 * @package Rendix2\FamilyTree\App\Filters
 */
interface IFilter
{

    /**
     * @param IEntity $entity
     *
     * @return string
     */
    //public function __invoke(IEntity $entity);
}
