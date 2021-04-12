<?php
/**
 *
 * Created by PhpStorm.
 * Filename: GenusManager.php
 * User: Tomáš Babický
 * Date: 02.04.2021
 * Time: 15:04
 */

namespace Rendix2\FamilyTree\App\Model\Managers;

use Rendix2\FamilyTree\App\Filters\GenusFilter;
use Rendix2\FamilyTree\App\Model\CrudManager\CrudManager;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultContainer;
use Rendix2\FamilyTree\App\Model\Managers\Genus\GenusTable;

/**
 * Class GenusManager
 *
 * @package Rendix2\FamilyTree\App\Model\Managers
 */
class GenusManager extends CrudManager
{
    /**
     * GenusManager constructor.
     *
     * @param DefaultContainer $defaultContainer
     * @param GenusFilter      $genusFilter
     * @param GenusTable       $table
     */
    public function __construct(
        DefaultContainer $defaultContainer,
        GenusFilter $genusFilter,
        GenusTable $table
    ) {
        parent::__construct($defaultContainer, $table, $genusFilter);
    }
}
