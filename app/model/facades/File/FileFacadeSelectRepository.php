<?php
/**
 *
 * Created by PhpStorm.
 * Filename: FileFacadeSelectRepository.php
 * User: Tomáš Babický
 * Date: 07.04.2021
 * Time: 0:26
 */

namespace Rendix2\FamilyTree\App\Model\Facades\File;

use Rendix2\FamilyTree\App\Model\Interfaces\ISelectRepository;

/**
 * Class FileFacadeSelectRepository
 *
 * @package Rendix2\FamilyTree\App\Model\Facades\File
 */
class FileFacadeSelectRepository implements ISelectRepository
{
    /**
     * @var FileFacadeSelector $fileFacadeSelector
     */
    private $fileFacadeSelector;

    /**
     * @var FileFacadeCachedSelector
     */
    private $fileFacadeCachedSelector;

    /**
     * FileFacadeSelectRepository constructor.
     *
     * @param FileFacadeSelector $fileFacadeSelector
     * @param FileFacadeCachedSelector $fileFacadeCachedSelector
     */
    public function __construct(
        FileFacadeSelector $fileFacadeSelector,
        FileFacadeCachedSelector $fileFacadeCachedSelector
    ) {
        $this->fileFacadeSelector = $fileFacadeSelector;
        $this->fileFacadeCachedSelector = $fileFacadeCachedSelector;
    }

    public function __destruct()
    {
        $this->fileFacadeSelector = null;
        $this->fileFacadeCachedSelector = null;
    }

    /**
     * @return FileFacadeSelector
     */
    public function getManager()
    {
        return $this->fileFacadeSelector;
    }

    /**
     * @return FileFacadeCachedSelector
     */
    public function getCachedManager()
    {
        return $this->fileFacadeCachedSelector;
    }
}