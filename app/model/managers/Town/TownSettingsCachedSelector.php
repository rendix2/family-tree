<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownSettingsCachedSelector.php
 * User: Tomáš Babický
 * Date: 03.04.2021
 * Time: 12:29
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Town;

use Nette\Caching\IStorage;

/**
 * Class TownSettingsCachedSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Town
 */
class TownSettingsCachedSelector extends TownCachedSelector
{
    /**
     * TownSettingsCachedSelector constructor.
     *
     * @param IStorage             $storage
     * @param TownSettingsSelector $selector
     */
    public function __construct(IStorage $storage, TownSettingsSelector $selector)
    {
        parent::__construct($storage, $selector);
    }
}
