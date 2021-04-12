<?php
/**
 *
 * Created by PhpStorm.
 * Filename: JobTable.php
 * User: Tomáš Babický
 * Date: 05.04.2021
 * Time: 1:34
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Job;

use Rendix2\FamilyTree\App\Model\Entities\JobEntity;
use Rendix2\FamilyTree\App\Model\Interfaces\ITable;
use Rendix2\FamilyTree\App\Model\Managers\Tables;

/**
 * Class JobTable
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Job
 */
class JobTable implements ITable
{
    public function getTableName()
    {
        return Tables::JOB_TABLE;
    }

    public function getEntity()
    {
        return JobEntity::class;
    }

    public function getPrimaryKey()
    {
        return 'id';
    }
}
