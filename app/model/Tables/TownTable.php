<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownTable.php
 * User: Tomáš Babický
 * Date: 03.04.2021
 * Time: 12:27
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Town;

use Rendix2\FamilyTree\App\Model\Entities\TownEntity;
use Rendix2\FamilyTree\App\Model\Interfaces\ITable;
use Rendix2\FamilyTree\App\Model\Managers\Tables;

/**
 * Class TownTable
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Town
 */
class TownTable implements ITable
{

    public function getTableName()
    {
        return Tables::TOWN_TABLE;
    }

    public function getEntity()
    {
        return TownEntity::class;
    }

    public function getPrimaryKey()
    {
        return 'id';
    }

    public function getColumns()
    {
        return [
            'id',
            'countryId',
            'name',
            'zipCode',
            'gps',
        ];
    }
}
