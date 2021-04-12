<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonSelectRepository.php
 * User: Tomáš Babický
 * Date: 03.04.2021
 * Time: 2:08
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Person;

use Rendix2\FamilyTree\App\Model\Managers\Person\Interfaces\IPersonSelectRepository;

class PersonSelectRepository implements IPersonSelectRepository
{
    /**
     * @var PersonCachedSelector $personCachedSelector
     */
    private $personCachedSelector;

    /**
     * @var PersonSelector $personSelector
     */
    private $personSelector;
    /**
     * @var PersonSettingsCachedSelector $personSettingsCachedSelector
     */
    private $personSettingsCachedSelector;

    /**
     * @var PersonSettingsSelector $personSettingsSelector
     */
    private $personSettingsSelector;

    /**
     * PersonSelectRepository constructor.
     *
     * @param PersonSelector               $personSelector
     * @param PersonCachedSelector         $personCachedSelector
     * @param PersonSettingsSelector       $personSettingsSelector
     * @param PersonSettingsCachedSelector $personSettingsCachedSelector
     */
    public function __construct(
        PersonSelector $personSelector,
        PersonCachedSelector $personCachedSelector,
        PersonSettingsSelector $personSettingsSelector,
        PersonSettingsCachedSelector $personSettingsCachedSelector
    ) {
        $this->personCachedSelector = $personCachedSelector;
        $this->personSelector = $personSelector;
        $this->personSettingsSelector = $personSettingsSelector;
        $this->personSettingsCachedSelector = $personSettingsCachedSelector;
    }

    public function getSettingsManager()
    {
        return $this->personSettingsSelector;
    }

    public function getSettingsCachedManager()
    {
        return $this->personSettingsCachedSelector;
    }

    public function getManager()
    {
        return $this->personSelector;
    }

    public function getCachedManager()
    {
        return $this->personCachedSelector;
    }
}
