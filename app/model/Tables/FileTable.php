<?php
/**
 *
 * Created by PhpStorm.
 * Filename: FileTable.php
 * User: Tomáš Babický
 * Date: 04.04.2021
 * Time: 20:04
 */

namespace Rendix2\FamilyTree\App\Model\Managers\File;

use Rendix2\FamilyTree\App\Model\Entities\FileEntity;
use Rendix2\FamilyTree\App\Model\Interfaces\ITable;
use Rendix2\FamilyTree\App\Model\Managers\Tables;

/**
 * Class FileTable
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\File
 */
class FileTable implements ITable
{
    public function getTableName()
    {
        return Tables::FILE_TABLE;
    }

    public function getEntity()
    {
        return FileEntity::class;
    }

    public function getPrimaryKey()
    {
        return 'id';
    }
}
