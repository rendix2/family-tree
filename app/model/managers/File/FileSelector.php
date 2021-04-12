<?php
/**
 *
 * Created by PhpStorm.
 * Filename: FIleSelector.php
 * User: Tomáš Babický
 * Date: 04.04.2021
 * Time: 20:08
 */

namespace Rendix2\FamilyTree\App\Model\Managers\File;

use Dibi\Connection;
use Rendix2\FamilyTree\App\Filters\FileFilter;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultSelector;
use Rendix2\FamilyTree\App\Model\Entities\FileEntity;
use Rendix2\FamilyTree\App\Model\Managers\File\Interfaces\IFileSelector;

/**
 * Class FileSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\File
 */
class FileSelector extends DefaultSelector implements IFileSelector
{
    public function __construct(
        Connection $connection,
        FileFilter $fileFilter,
        FileTable $table
    ) {
        parent::__construct($connection, $table, $fileFilter);
    }

    public function getByPersonId($personId)
    {
        return $this->getAllFluent()
            ->where('[personId] = %i', $personId)
            ->execute()
            ->setRowClass(FileEntity::class)
            ->fetchAll();
    }
}