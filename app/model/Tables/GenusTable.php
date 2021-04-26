<?php
/**
 *
 * Created by PhpStorm.
 * Filename: GenusTable.php
 * User: Tomáš Babický
 * Date: 04.04.2021
 * Time: 2:18
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Genus;

use Rendix2\FamilyTree\App\Model\Entities\GenusEntity;
use Rendix2\FamilyTree\App\Model\Interfaces\ITable;
use Rendix2\FamilyTree\App\Model\Managers\Tables;

/**
 * Class GenusTable
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Genus
 */
class GenusTable implements ITable
{
    public function getTableName()
    {
        return Tables::GENUS_TABLE;
    }

    public function getEntity()
    {
       return GenusEntity::class;
    }

    public function getPrimaryKey()
    {
        return 'id';
    }

    public function getColumns()
    {
        return [
          'id',
          'surname',
          'surnameFonetic'
        ];
    }
}
