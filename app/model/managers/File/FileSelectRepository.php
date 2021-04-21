<?php
/**
 *
 * Created by PhpStorm.
 * Filename: FileSelectRepository.php
 * User: Tomáš Babický
 * Date: 04.04.2021
 * Time: 20:10
 */

namespace Rendix2\FamilyTree\App\Model\Managers\File;

use Dibi\Connection;
use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Filters\FileFilter;
use Rendix2\FamilyTree\App\Model\Interfaces\ISelectRepository;

/**
 * Class FileSelectRepository
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\File
 */
class FileSelectRepository implements ISelectRepository
{
    /**
     * @var FileCachedSelector $fileCachedSelector
     */
    private $fileCachedSelector;

    /**
     * @var FileSelector $fileSelector
     */
    private $fileSelector;

    /**
     * FileSelectRepository constructor.
     *
     * @param Connection         $connection
     * @param IStorage           $storage
     * @param FileFilter         $fileFilter
     * @param FileTable          $table
     * @param FileSelector       $fileSelector
     * @param FileCachedSelector $fileCachedSelector
     */
    public function __construct(
        FileSelector $fileSelector,
        FileCachedSelector $fileCachedSelector
    ) {
        $this->fileSelector = $fileSelector;
        $this->fileCachedSelector = $fileCachedSelector;
    }

    public function __destruct()
    {
        $this->fileSelector = null;
        $this->fileCachedSelector = null;
    }

    /**
     * @return FileSelector
     */
    public function getManager()
    {
        return $this->fileSelector;
    }

    /**
     * @return FileCachedSelector
     */
    public function getCachedManager()
    {
        return $this->fileCachedSelector;
    }
}
