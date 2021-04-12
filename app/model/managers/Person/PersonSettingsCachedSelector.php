<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonSettingsCachedSelector.php
 * User: Tomáš Babický
 * Date: 03.04.2021
 * Time: 4:03
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Person;

use Nette\Caching\IStorage;

/**
 * Class PersonSettingsCachedSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Person
 */
class PersonSettingsCachedSelector extends PersonCachedSelector
{
    /**
     * PersonSettingsCachedSelector constructor.
     *
     * @param IStorage               $storage
     * @param PersonSettingsSelector $selector
     */
    public function __construct(
        IStorage $storage,
        PersonSettingsSelector $selector
    ) {
        parent::__construct($storage, $selector);
    }
}
