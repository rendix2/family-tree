<?php
/**
 *
 * Created by PhpStorm.
 * Filename: ISelect.php
 * User: Tomáš Babický
 * Date: 02.04.2021
 * Time: 20:50
 */

namespace Rendix2\FamilyTree\App\Model\Interfaces;

use Dibi\Fluent;

/**
 * Interface ISelect
 *
 * @package Rendix2\FamilyTree\App\Model\Interfaces
 */
interface ISelector
{
    public function getByPrimaryKey($id);

    public function getByPrimaryKeys(array $ids);

    public function getColumnFluent($column);

    public function getAll();

    public function getAllPairs();

    public function getPairs($column);

    public function getBySubQuery(Fluent $query);
}
