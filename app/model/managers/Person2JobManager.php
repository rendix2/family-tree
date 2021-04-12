<?php
/**
 *
 * Created by PhpStorm.
 * Filename: Person2JobManager.php
 * User: Tomáš Babický
 * Date: 02.04.2021
 * Time: 15:13
 */

namespace Rendix2\FamilyTree\App\Model\Managers;

use Rendix2\FamilyTree\App\Model\CrudManager\DefaultContainer;
use Rendix2\FamilyTree\App\Model\Managers\Job\JobTable;
use Rendix2\FamilyTree\App\Model\Managers\M2NManger\M2NManager;
use Rendix2\FamilyTree\App\Model\Managers\Person\PersonTable;
use Rendix2\FamilyTree\App\Model\Tables\Person2JobTable;

/**
 * Class Person2JobManager
 *
 * @package Rendix2\FamilyTree\App\Model\Managers
 */
class Person2JobManager extends M2NManager
{
    /**
     * Person2JobManager constructor.
     *
     * @param DefaultContainer $defaultContainer
     * @param Person2JobTable  $table
     * @param PersonTable      $leftTable
     * @param JobTable         $rightTable
     */
    public function __construct(
        DefaultContainer $defaultContainer,
        Person2JobTable $table,
        PersonTable $leftTable,
        JobTable $rightTable
    ) {
        parent::__construct($defaultContainer, $table, $leftTable, $rightTable);
    }
}