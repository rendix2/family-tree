<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressTable.php
 * User: Tomáš Babický
 * Date: 03.04.2021
 * Time: 23:24
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Address;

use Rendix2\FamilyTree\App\Model\Entities\AddressEntity;
use Rendix2\FamilyTree\App\Model\Interfaces\ITable;
use Rendix2\FamilyTree\App\Model\Managers\Tables;

/**
 * Class AddressTable
 *
 * @package Rendix2\FamilyTree\App\Model\Managers
 */
class AddressTable implements ITable
{

    public function getTableName()
    {
        return Tables::ADDRESS_TABLE;
    }

    public function getEntity()
    {
        return AddressEntity::class;
    }

    public function getPrimaryKey()
    {
        return 'id';
    }

    public function getColumns()
    {
        return [
            'id',
            'street',
            'streetNumber',
            'houseNumber',
            'townId',
            'countryId',
            'gps'
        ];
    }
}
