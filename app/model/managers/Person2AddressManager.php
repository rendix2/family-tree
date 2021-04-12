<?php
/**
 *
 * Created by PhpStorm.
 * Filename: Person2AddressManager.php
 * User: Tomáš Babický
 * Date: 02.04.2021
 * Time: 15:13
 */

namespace Rendix2\FamilyTree\App\Model\Managers;

use Rendix2\FamilyTree\App\Model\CrudManager\DefaultContainer;
use Rendix2\FamilyTree\App\Model\Managers\Address\AddressTable;
use Rendix2\FamilyTree\App\Model\Managers\M2NManger\M2NManager;
use Rendix2\FamilyTree\App\Model\Managers\Person\PersonTable;
use Rendix2\FamilyTree\App\Model\Tables\Person2AddressTable;

/**
 * Class Person2AddressManager
 *
 * @package Rendix2\FamilyTree\App\Model\Managers
 */
class Person2AddressManager extends M2NManager
{
    /**
     * Person2AddressManager constructor.
     *
     * @param DefaultContainer    $defaultContainer
     * @param Person2AddressTable $table
     * @param PersonTable         $leftTable
     * @param AddressTable        $rightTable
     */
    public function __construct(
        DefaultContainer $defaultContainer,
        Person2AddressTable $table,
        PersonTable $leftTable,
        AddressTable $rightTable
    ) {
        parent::__construct($defaultContainer, $table, $leftTable, $rightTable);
    }
}
