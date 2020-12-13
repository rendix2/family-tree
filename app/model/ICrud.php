<?php
/**
 *
 * Created by PhpStorm.
 * Filename: ICrud.php
 * User: Tomáš Babický
 * Date: 11.12.2020
 * Time: 14:55
 */

namespace Rendix2\FamilyTree\App\Model;

use Dibi\Fluent;

/**
 * Interface ICrud
 *
 * @package Rendix2\FamilyTree\App\Model
 */
interface ICrud
{
    public function getColumnFluent($column);

    public function getAllFluent();

    public function getAll();

    public function getByPrimaryKey($id);

    public function getByPrimaryKeys(array $ids);

    public function getBySubQuery(Fluent $query);
}
