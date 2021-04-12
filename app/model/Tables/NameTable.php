<?php
/**
 *
 * Created by PhpStorm.
 * Filename: NameTable.php
 * User: Tomáš Babický
 * Date: 06.04.2021
 * Time: 23:02
 */

namespace Rendix2\FamilyTree\App\Model\Tables;

use Rendix2\FamilyTree\App\Model\Entities\NameEntity;
use Rendix2\FamilyTree\App\Model\Interfaces\ITable;
use Rendix2\FamilyTree\App\Model\Managers\Tables;

/**
 * Class NameTable
 *
 * @package Rendix2\FamilyTree\App\Model\Tables
 */
class NameTable implements ITable
{

    public function getTableName()
    {
        return Tables::NAME_TABLE;
    }

    public function getEntity()
    {
        return NameEntity::class;
    }

    public function getPrimaryKey()
    {
        return 'id';
    }
}
