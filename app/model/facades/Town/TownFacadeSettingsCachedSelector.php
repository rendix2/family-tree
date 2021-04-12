<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownFacadeSettingsCachedSelector.php
 * User: Tomáš Babický
 * Date: 10.04.2021
 * Time: 14:49
 */

namespace Rendix2\FamilyTree\App\Model\Facades\Town;

use Nette\Caching\IStorage;

/**
 * Class TownFacadeSettingsCachedSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Facades\Town
 */
class TownFacadeSettingsCachedSelector extends TownFacadeCachedSelector
{
    /**
     * TownFacadeSettingsCachedSelector constructor.
     *
     * @param IStorage                   $storage
     * @param TownFacadeSettingsSelector $selector
     */
    public function __construct(
        IStorage $storage,
        TownFacadeSettingsSelector $selector
    ) {
        parent::__construct($storage, $selector);
    }
}
