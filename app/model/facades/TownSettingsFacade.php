<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownSettingsFacade.php
 * User: Tomáš Babický
 * Date: 13.02.2021
 * Time: 19:46
 */

namespace Rendix2\FamilyTree\App\Model\Facades;

use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Managers\CountryManager;
use Rendix2\FamilyTree\App\Managers\TownSettingsManager;

/**
 * Class TownSettingsFacade
 *
 * @package Rendix2\FamilyTree\App\Model\Facades
 */
class TownSettingsFacade extends TownFacade
{
    /**
     * TownSettingsFacade constructor.
     *
     * @param IStorage $storage
     * @param CountryManager $countryManager
     * @param TownSettingsManager $townSettingsManager
     */
    public function __construct(IStorage $storage, CountryManager $countryManager, TownSettingsManager $townSettingsManager)
    {
        parent::__construct($storage, $countryManager, $townSettingsManager);
    }
}
