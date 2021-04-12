<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceTable.php
 * User: Tomáš Babický
 * Date: 06.04.2021
 * Time: 2:54
 */

namespace Rendix2\FamilyTree\App\Model\Table;


use Rendix2\FamilyTree\App\Model\Entities\SourceEntity;
use Rendix2\FamilyTree\App\Model\Interfaces\ITable;
use Rendix2\FamilyTree\App\Model\Managers\Tables;

class SourceTable implements ITable
{

    public function getTableName()
    {
        return Tables::SOURCE_TABLE;
    }

    public function getEntity()
    {
        return SourceEntity::class;
    }

    public function getPrimaryKey()
    {
        return 'id';
    }
}