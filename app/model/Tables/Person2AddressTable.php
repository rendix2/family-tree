<?php
/**
 *
 * Created by PhpStorm.
 * Filename: Person2AddressTable.php
 * User: Tomáš Babický
 * Date: 06.04.2021
 * Time: 0:08
 */

namespace Rendix2\FamilyTree\App\Model\Tables;

use Rendix2\FamilyTree\App\Model\Entities\Person2AddressEntity;
use Rendix2\FamilyTree\App\Model\Managers\M2NManger\Interfaces\IM2NTable;
use Rendix2\FamilyTree\App\Model\Managers\Tables;

/**
 * Class Person2AddressTable
 *
 * @package Rendix2\FamilyTree\App\Model\Tables
 */
class Person2AddressTable implements IM2NTable
{
    public function getTableName()
    {
        return Tables::PERSON2ADDRESS_TABLE;
    }

    public function getEntity()
    {
        return Person2AddressEntity::class;
    }

    public function getLeftPrimaryKey()
    {
        return 'personId';
    }

    public function getRightPrimaryKey()
    {
        return 'addressId';
    }

    public function getPrimaryKey()
    {
        return [
            $this->getLeftPrimaryKey(),
            $this->getRightPrimaryKey()
        ];
    }
}
