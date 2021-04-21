<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownSelectRepository.php
 * User: Tomáš Babický
 * Date: 03.04.2021
 * Time: 12:29
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Town;

use Rendix2\FamilyTree\App\Model\Interfaces\ISelectRepository;

/**
 * Class TownSelectRepository
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Town
 */
class TownSelectRepository implements ISelectRepository
{
    /**
     * @var TownCachedSelector $townCachedSelector
     */
    private $townCachedSelector;

    /**
     * @var TownSelector $townSelector
     */
    private $townSelector;

    /**
     * @var TownSettingsCachedSelector $townSettingsCachedSelector
     */
    private $townSettingsCachedSelector;

    /**
     * @var TownSettingsSelector $townSettingsSelector
     */
    private $townSettingsSelector;

    /**
     * TownSelectRepository constructor.
     *
     * @param TownCachedSelector         $townCachedSelector
     * @param TownSelector               $townSelector
     * @param TownSettingsCachedSelector $townSettingsCachedSelector
     * @param TownSettingsSelector       $townSettingsSelector
     */
    public function __construct(
        TownCachedSelector $townCachedSelector,
        TownSelector $townSelector,
        TownSettingsCachedSelector $townSettingsCachedSelector,
        TownSettingsSelector $townSettingsSelector
    ) {
        $this->townCachedSelector = $townCachedSelector;
        $this->townSelector = $townSelector;
        $this->townSettingsCachedSelector = $townSettingsCachedSelector;
        $this->townSettingsSelector = $townSettingsSelector;
    }

    public function __destruct()
    {
        $this->townSettingsSelector = null;
        $this->townSelector = null;

        $this->townCachedSelector = null;
        $this->townSettingsCachedSelector = null;
    }

    /**
     * @return TownSelector
     */
    public function getManager()
    {
        return $this->townSelector;
    }

    /**
     * @return TownCachedSelector
     */
    public function getCachedManager()
    {
        return $this->townCachedSelector;
    }

    /**
     * @return TownSettingsSelector
     */
    public function getSettingsManager()
    {
        return $this->townSettingsSelector;
    }

    /**
     * @return TownSettingsCachedSelector
     */
    public function getSettingsCachedManager()
    {
        return $this->townSettingsCachedSelector;
    }
}
