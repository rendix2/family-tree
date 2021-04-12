<?php
/**
 *
 * Created by PhpStorm.
 * Filename: IM2NTable.php
 * User: Tomáš Babický
 * Date: 05.04.2021
 * Time: 21:32
 */

namespace Rendix2\FamilyTree\App\Model\Managers\M2NManger\Interfaces;

use Rendix2\FamilyTree\App\Model\Interfaces\ITable;

/**
 * Interface IM2NTable
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\M2NManger\Interfaces
 */
interface IM2NTable extends ITable
{
    public function getLeftPrimaryKey();

    public function getRightPrimaryKey();
}
