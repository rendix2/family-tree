<?php
/**
 *
 * Created by PhpStorm.
 * Filename: FileFilter.php
 * User: Tomáš Babický
 * Date: 15.12.2020
 * Time: 10:45
 */

namespace Rendix2\FamilyTree\App\Filters;

use Rendix2\FamilyTree\App\Model\Entities\FileEntity;

/**
 * Class FileFilter
 *
 * @package Rendix2\FamilyTree\App\Filters
 */
class FileFilter implements IFilter
{
    /**
     * @param FileEntity $fileEntity
     *
     * @return string
     */
    public function __invoke(FileEntity $fileEntity)
    {
        return $fileEntity->originName . '.' . $fileEntity->extension;
    }
}
