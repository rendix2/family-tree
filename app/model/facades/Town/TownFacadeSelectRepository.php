<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownFacadeSelectRepository.php
 * User: Tomáš Babický
 * Date: 10.04.2021
 * Time: 14:49
 */

namespace Rendix2\FamilyTree\App\Model\Facades\Town;


use Rendix2\FamilyTree\App\Model\Interfaces\ISettingsSelectRepository;

/**
 * Class TownFacadeSelectRepository
 *
 * @package Rendix2\FamilyTree\App\Model\Facades\Town
 */
class TownFacadeSelectRepository implements ISettingsSelectRepository
{
    /**
     * @var TownFacadeCachedSelector $townFacadeCachedSelector
     */
    private $townFacadeCachedSelector;

    /**
     * @var TownFacadeSelector
     */
    private $townFacadeSelector;

    /**
     * @var TownFacadeSettingsCachedSelector $townFacadeSettingsCachesSelector
     */
    private $townFacadeSettingsCachesSelector;

    /**
     * @var TownFacadeSettingsSelector $townFacadeSettingsSelector
     */
    private $townFacadeSettingsSelector;

    /**
     * TownFacadeSelectRepository constructor.
     *
     * @param TownFacadeCachedSelector $townFacadeCachedSelector
     * @param TownFacadeSelector $townFacadeSelector
     * @param TownFacadeSettingsCachedSelector $townFacadeSettingsCachesSelector
     * @param TownFacadeSettingsSelector $townFacadeSettingsSelector
     */
    public function __construct(
        TownFacadeCachedSelector $townFacadeCachedSelector,
        TownFacadeSelector $townFacadeSelector,
        TownFacadeSettingsCachedSelector $townFacadeSettingsCachesSelector,
        TownFacadeSettingsSelector $townFacadeSettingsSelector
    ) {
        $this->townFacadeCachedSelector = $townFacadeCachedSelector;
        $this->townFacadeSelector = $townFacadeSelector;
        $this->townFacadeSettingsCachesSelector = $townFacadeSettingsCachesSelector;
        $this->townFacadeSettingsSelector = $townFacadeSettingsSelector;
    }

    /**
     * @return TownFacadeSelector
     */
    public function getManager()
    {
        return $this->townFacadeSelector;
    }

    /**
     * @return TownFacadeCachedSelector
     */
    public function getCachedManager()
    {
        return $this->townFacadeCachedSelector;
    }

    /**
     * @return TownFacadeSettingsSelector
     */
    public function getSettingsManager()
    {
        return $this->townFacadeSettingsSelector;
    }

    /**
     * @return TownFacadeSettingsCachedSelector
     */
    public function getSettingsCachedManager()
    {
        return $this->townFacadeSettingsCachesSelector;
    }
}