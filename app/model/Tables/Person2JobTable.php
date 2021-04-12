<?php
/**
 *
 * Created by PhpStorm.
 * Filename: Person2JobTable.php
 * User: Tomáš Babický
 * Date: 06.04.2021
 * Time: 0:10
 */

namespace Rendix2\FamilyTree\App\Model\Tables;

use Rendix2\FamilyTree\App\Model\Entities\Person2JobEntity;
use Rendix2\FamilyTree\App\Model\Managers\M2NManger\Interfaces\IM2NTable;
use Rendix2\FamilyTree\App\Model\Managers\Tables;

/**
 * Class Person2JobTable
 *
 * @package Rendix2\FamilyTree\App\Model\Tables
 */
class Person2JobTable implements IM2NTable
{
    public function getTableName()
    {
        return Tables::PERSON2JOB_TABLE;
    }

    public function getEntity()
    {
        return Person2JobEntity::class;
    }

    public function getLeftPrimaryKey()
    {
        return 'personId';
    }

    public function getRightPrimaryKey()
    {
        return 'jobId';
    }

    public function getPrimaryKey()
    {
        return [
            $this->getLeftPrimaryKey(),
            $this->getRightPrimaryKey()
        ];
    }
}
