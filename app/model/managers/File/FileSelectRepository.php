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
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultSelectRepository;

/**
 * Class FileSelectRepository
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\File
 */
class FileSelectRepository extends DefaultSelectRepository
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
        Connection $connection,
        IStorage $storage,
        FileFilter $fileFilter,
        FileTable $table,
        FileSelector $fileSelector,
        FileCachedSelector $fileCachedSelector
    ) {
        parent::__construct($connection, $storage, $table, $fileFilter);

        $this->fileSelector = $fileSelector;
        $this->fileCachedSelector = $fileCachedSelector;
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
