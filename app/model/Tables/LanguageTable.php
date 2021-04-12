<?php
/**
 *
 * Created by PhpStorm.
 * Filename: LanguageTable.php
 * User: Tomáš Babický
 * Date: 05.04.2021
 * Time: 1:12
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Language;

use Rendix2\FamilyTree\App\Model\Entities\LanguageEntity;
use Rendix2\FamilyTree\App\Model\Interfaces\ITable;
use Rendix2\FamilyTree\App\Model\Managers\Tables;

/**
 * Class LanguageTable
 */
class LanguageTable implements ITable
{
    public function getTableName()
    {
        return Tables::LANGUAGE_TABLE;
    }

    public function getEntity()
    {
        return LanguageEntity::class;
    }

    public function getPrimaryKey()
    {
        return  'id';
    }
}
