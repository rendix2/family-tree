<?php
/**
 *
 * Created by PhpStorm.
 * Filename: NameFacadeSelectRepository.php
 * User: Tomáš Babický
 * Date: 10.04.2021
 * Time: 2:15
 */

namespace Rendix2\FamilyTree\App\Model\Facades\Name;

use Rendix2\FamilyTree\App\Model\Interfaces\ISelectRepository;

/**
 * Class NameFacadeSelectRepository
 *
 * @package Rendix2\FamilyTree\App\Model\Facades\Name
 */
class NameFacadeSelectRepository implements ISelectRepository
{
    /**
     * @var NameFacadeCachedSelector $nameFacadeCachedSelector
     */
    private $nameFacadeCachedSelector;

    /**
     * @var NameFacadeSelector $nameFacadeSelector
     */
    private $nameFacadeSelector;

    /**
     * NameFacadeSelectRepository constructor.
     *
     * @param NameFacadeCachedSelector $nameFacadeCachedSelector
     * @param NameFacadeSelector       $nameFacadeSelector
     */
    public function __construct(
        NameFacadeCachedSelector $nameFacadeCachedSelector,
        NameFacadeSelector $nameFacadeSelector
    ) {
        $this->nameFacadeCachedSelector = $nameFacadeCachedSelector;
        $this->nameFacadeSelector = $nameFacadeSelector;
    }

    public function __destruct()
    {
        $this->nameFacadeSelector = null;
        $this->nameFacadeCachedSelector = null;
    }

    /**
     * @return NameFacadeSelector
     */
    public function getManager()
    {
        return $this->nameFacadeSelector;
    }

    /**
     * @return NameFacadeCachedSelector
     */
    public function getCachedManager()
    {
        return $this->nameFacadeCachedSelector;
    }
}
