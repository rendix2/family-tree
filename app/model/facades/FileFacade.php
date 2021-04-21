<?php
/**
 *
 * Created by PhpStorm.
 * Filename: FileFacade.php
 * User: Tomáš Babický
 * Date: 15.12.2020
 * Time: 10:33
 */

namespace Rendix2\FamilyTree\App\Model\Facades;

use Rendix2\FamilyTree\App\Model\CrudManager\DefaultContainer;
use Rendix2\FamilyTree\App\Model\Facades\DefaultFacade\DefaultFacade;
use Rendix2\FamilyTree\App\Model\Facades\File\FileFacadeSelectRepository;
use Rendix2\FamilyTree\App\Model\Managers\File\FileTable;
use Rendix2\FamilyTree\App\Model\Managers\FileManager;

/**
 * Class FileFacade
 *
 * @package Rendix2\FamilyTree\App\Model\Facades
 */
class FileFacade extends DefaultFacade
{
    /**
     * @var FileFacadeSelectRepository $fileFacadeSelectRepository
     */
    private $fileFacadeSelectRepository;

    /**
     * FileFacade constructor.
     *
     * @param DefaultContainer           $defaultContainer
     * @param FileFacadeSelectRepository $fileFacadeSelectRepository
     * @param FileManager                $fileManager
     * @param FileTable                  $fileTable
     */
    public function __construct(
        DefaultContainer $defaultContainer,
        FileFacadeSelectRepository $fileFacadeSelectRepository,
        FileManager $fileManager,
        FileTable $fileTable
    ) {
        parent::__construct($defaultContainer, $fileTable, $fileManager);

        $this->fileFacadeSelectRepository = $fileFacadeSelectRepository;
    }

    public function __destruct()
    {
        $this->fileFacadeSelectRepository = null;

        parent::__destruct();
    }

    /**
     * @return FileFacadeSelectRepository
     */
    public function select()
    {
        return $this->fileFacadeSelectRepository;
    }
}
