<?php
/**
 *
 * Created by PhpStorm.
 * Filename: FileManager.php
 * User: Tomáš Babický
 * Date: 02.04.2021
 * Time: 15:04
 */

namespace Rendix2\FamilyTree\App\Model\Managers;

use Rendix2\FamilyTree\App\Filters\FileFilter;
use Rendix2\FamilyTree\App\Model\CrudManager\CrudManager;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultContainer;
use Rendix2\FamilyTree\App\Model\Managers\File\FileSelectRepository;
use Rendix2\FamilyTree\App\Model\Managers\File\FileTable;

/**
 * Class FileManager
 *
 * @package Rendix2\FamilyTree\App\Model\Managers
 */
class FileManager extends CrudManager
{
    /**
     * @var FileSelectRepository $fileSelectRepository
     */
    private $fileSelectRepository;

    /**
     * FileManager constructor.
     *
     * @param DefaultContainer     $defaultContainer
     * @param FileTable            $table
     * @param FileFilter           $fileFilter
     * @param FileSelectRepository $fileSelectRepository
     */
    public function __construct(
        DefaultContainer $defaultContainer,
        FileTable $table,
        FileFilter $fileFilter,
        FileSelectRepository $fileSelectRepository
    ) {
        parent::__construct($defaultContainer, $table, $fileFilter);

        $this->fileSelectRepository = $fileSelectRepository;
    }

    public function __destruct()
    {
        $this->fileSelectRepository = null;

        parent::__destruct();
    }

    /**
     * @return FileSelectRepository
     */
    public function select()
    {
        return $this->fileSelectRepository;
    }
}
