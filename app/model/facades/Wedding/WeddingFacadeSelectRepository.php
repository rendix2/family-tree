<?php
/**
 *
 * Created by PhpStorm.
 * Filename: WeddingFacadeSelectRepository.php
 * User: Tomáš Babický
 * Date: 12.04.2021
 * Time: 2:10
 */

namespace Rendix2\FamilyTree\App\Model\Facades\Wedding;

use Rendix2\FamilyTree\App\Model\Interfaces\ISelectRepository;

/**
 * Class WeddingFacadeSelectRepository
 *
 * @package Rendix2\FamilyTree\App\Model\Facades\Wedding
 */
class WeddingFacadeSelectRepository implements ISelectRepository
{
    /**
     * @var WeddingFacadeCachedSelector $weddingFacadeCachedSelector
     */
    private $weddingFacadeCachedSelector;

    /**
     * @var WeddingFacadeSelector $weddingFacadeSelector
     */
    private $weddingFacadeSelector;

    /**
     * WeddingFacadeSelectRepository constructor.
     *
     * @param WeddingFacadeCachedSelector $weddingFacadeCachedSelector
     * @param WeddingFacadeSelector       $weddingFacadeSelector
     */
    public function __construct(
        WeddingFacadeCachedSelector $weddingFacadeCachedSelector,
        WeddingFacadeSelector $weddingFacadeSelector
    ) {
        $this->weddingFacadeCachedSelector = $weddingFacadeCachedSelector;
        $this->weddingFacadeSelector = $weddingFacadeSelector;
    }

    public function __destruct()
    {
        $this->weddingFacadeSelector = null;
        $this->weddingFacadeCachedSelector = null;
    }

    /**
     * @return WeddingFacadeSelector
     */
    public function getManager()
    {
        return $this->weddingFacadeSelector;
    }

    /**
     * @return WeddingFacadeCachedSelector
     */
    public function getCachedManager()
    {
        return $this->weddingFacadeCachedSelector;
    }
}
