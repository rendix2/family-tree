<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceTypeTable.php
 * User: Tomáš Babický
 * Date: 04.04.2021
 * Time: 3:00
 */

namespace Rendix2\FamilyTree\App\Model\Managers\SourceType;

use Rendix2\FamilyTree\App\Model\Entities\SourceTypeEntity;
use Rendix2\FamilyTree\App\Model\Interfaces\ITable;
use Rendix2\FamilyTree\App\Model\Managers\Tables;

/**
 * Class SourceTypeTable
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\SourceType
 */
class SourceTypeTable implements ITable
{
    public function getTableName()
    {
        return Tables::SOURCE_TYPE_TABLE;
    }

    public function getEntity()
    {
        return SourceTypeEntity::class;
    }

    public function getPrimaryKey()
    {
        return 'id';
    }
}
