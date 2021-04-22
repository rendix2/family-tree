<?php
/**
 *
 * Created by PhpStorm.
 * Filename: CountryTable.php
 * User: Tomáš Babický
 * Date: 03.04.2021
 * Time: 22:59
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Country;


use Rendix2\FamilyTree\App\Model\Entities\CountryEntity;
use Rendix2\FamilyTree\App\Model\Interfaces\ITable;
use Rendix2\FamilyTree\App\Model\Managers\Tables;

class CountryTable implements ITable
{

    public function getTableName()
    {
        return Tables::COUNTRY_TABLE;
    }

    public function getEntity()
    {
       return CountryEntity::class;
    }

    public function getPrimaryKey()
    {
        return 'id';
    }

    public function getColumns()
    {
        return [
          'id',
            'name'
        ];
    }
}
