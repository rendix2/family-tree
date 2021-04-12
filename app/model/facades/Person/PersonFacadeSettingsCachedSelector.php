<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonFacadeSettingsCachedSelector.php
 * User: Tomáš Babický
 * Date: 10.04.2021
 * Time: 2:47
 */

namespace Rendix2\FamilyTree\App\Model\Facades\Person;

use Nette\Caching\IStorage;

/**
 * Class PersonFacadeSettingsCachedSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Facades\Person
 */
class PersonFacadeSettingsCachedSelector extends PersonFacadeCachedSelector
{
    /**
     * PersonFacadeSettingsCachedSelector constructor.
     *
     * @param IStorage                     $storage
     * @param PersonFacadeSettingsSelector $selector
     */
    public function __construct(
        IStorage $storage,
        PersonFacadeSettingsSelector $selector
    ) {
        parent::__construct($storage, $selector);
    }
}
