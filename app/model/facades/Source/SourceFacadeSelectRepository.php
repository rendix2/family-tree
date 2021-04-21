<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceSelectRepository.php
 * User: Tomáš Babický
 * Date: 06.04.2021
 * Time: 3:20
 */

namespace Rendix2\FamilyTree\App\Model\Facades\Source;

use Rendix2\FamilyTree\App\Model\Interfaces\ISelectRepository;

/**
 * Class SourceSelectRepository
 *
 * @package Rendix2\FamilyTree\App\Model\Facades\Source
 */
class SourceFacadeSelectRepository implements ISelectRepository
{
    /**
     * @var SourceFacadeCachedSelector $sourceFacadeCachedSelector
     */
    private $sourceFacadeCachedSelector;

    /**
     * @var SourceFacadeSelector $sourceFacadeSelector
     */
    private $sourceFacadeSelector;

    /**
     * SourceSelectRepository constructor.
     *
     * @param SourceFacadeCachedSelector $sourceFacadeCachedSelector
     * @param SourceFacadeSelector       $sourceFacadeSelector
     */
    public function __construct(
        SourceFacadeCachedSelector $sourceFacadeCachedSelector,
        SourceFacadeSelector $sourceFacadeSelector
    ) {
        $this->sourceFacadeCachedSelector = $sourceFacadeCachedSelector;
        $this->sourceFacadeSelector = $sourceFacadeSelector;
    }

    public function __destruct()
    {
        $this->sourceFacadeSelector = null;
        $this->sourceFacadeCachedSelector = null;
    }

    /**
     * @return SourceFacadeSelector
     */
    public function getManager()
    {
        return $this->sourceFacadeSelector;
    }

    /**
     * @return SourceFacadeCachedSelector
     */
    public function getCachedManager()
    {
        return $this->sourceFacadeCachedSelector;
    }
}