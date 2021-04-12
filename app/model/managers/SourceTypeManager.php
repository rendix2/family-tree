<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceTypeManager.php
 * User: Tomáš Babický
 * Date: 02.04.2021
 * Time: 15:16
 */

namespace Rendix2\FamilyTree\App\Model\Managers;

use Rendix2\FamilyTree\App\Filters\SourceTypeFilter;
use Rendix2\FamilyTree\App\Model\CrudManager\CrudManager;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultContainer;
use Rendix2\FamilyTree\App\Model\Managers\SourceType\SourceTypeTable;

/**
 * Class SourceTypeManager
 *
 * @package Rendix2\FamilyTree\App\Model\Managers
 */
class SourceTypeManager extends CrudManager
{
    /**
     * SourceTypeManager constructor.
     *
     * @param DefaultContainer $defaultContainer
     * @param SourceTypeFilter $sourceTypeFilter
     * @param SourceTypeTable  $table
     */
    public function __construct(
        DefaultContainer $defaultContainer,
        SourceTypeFilter $sourceTypeFilter,
        SourceTypeTable $table
    ) {
        parent::__construct($defaultContainer, $table, $sourceTypeFilter);
    }
}
