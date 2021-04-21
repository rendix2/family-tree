<?php
/**
 *
 * Created by PhpStorm.
 * Filename: ITable.php
 * User: Tomáš Babický
 * Date: 02.04.2021
 * Time: 20:43
 */

namespace Rendix2\FamilyTree\App\Model\Interfaces;

/**
 * Interface ITable
 *
 * @package Rendix2\FamilyTree\App\Model\Interfaces
 */
interface ITable
{
    public function getTableName();

    public function getEntity();

    public function getPrimaryKey();
}
