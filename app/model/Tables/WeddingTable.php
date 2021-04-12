<?php
/**
 *
 * Created by PhpStorm.
 * Filename: WeddingTable.php
 * User: Tomáš Babický
 * Date: 05.04.2021
 * Time: 1:37
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Wedding;

use Rendix2\FamilyTree\App\Model\Entities\WeddingEntity;
use Rendix2\FamilyTree\App\Model\Interfaces\ITable;
use Rendix2\FamilyTree\App\Model\Managers\Tables;

/**
 * Class WeddingTable
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Wedding
 */
class WeddingTable implements ITable
{
    public function getTableName()
    {
        return Tables::WEDDING_TABLE;
    }

    public function getEntity()
    {
        return WeddingEntity::class;
    }

    public function getPrimaryKey()
    {
        return 'id';
    }
}
