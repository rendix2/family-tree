<?php
/**
 *
 * Created by PhpStorm.
 * Filename: RelationTable.php
 * User: Tomáš Babický
 * Date: 04.04.2021
 * Time: 3:09
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Relation;

use Rendix2\FamilyTree\App\Model\Entities\RelationEntity;
use Rendix2\FamilyTree\App\Model\Interfaces\ITable;
use Rendix2\FamilyTree\App\Model\Managers\Tables;

/**
 * Class RelationTable
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Relation
 */
class RelationTable implements ITable
{
    public function getTableName()
    {
        return Tables::RELATION_TABLE;
    }

    public function getEntity()
    {
        return RelationEntity::class;
    }

    public function getPrimaryKey()
    {
        return 'id';
    }
}
