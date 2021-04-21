<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonFacadeSelectRepository.php
 * User: Tomáš Babický
 * Date: 10.04.2021
 * Time: 2:47
 */

namespace Rendix2\FamilyTree\App\Model\Facades\Person;

use Rendix2\FamilyTree\App\Model\Interfaces\ISettingsSelectRepository;

/**
 * Class PersonFacadeSelectRepository
 *
 * @package Rendix2\FamilyTree\App\Model\Facades\Person
 */
class PersonFacadeSelectRepository implements ISettingsSelectRepository
{
    /**
     * @var PersonFacadeCachedSelector $personFacadeCachedSelector
     */
    private $personFacadeCachedSelector;

    /**
     * @var PersonFacadeSelector $personFacadeSelector
     */
    private $personFacadeSelector;

    /**
     * @var PersonFacadeSettingsCachedSelector $personFacadeSettingsCachedSelector
     */
    private $personFacadeSettingsCachedSelector;

    /**
     * @var PersonFacadeSettingsSelector $personFacadeSettingsSelector
     */
    private $personFacadeSettingsSelector;

    /**
     * PersonFacadeSelectRepository constructor.
     *
     * @param PersonFacadeCachedSelector         $personFacadeCachedSelector
     * @param PersonFacadeSelector               $personFacadeSelector
     * @param PersonFacadeSettingsCachedSelector $personFacadeSettingsCachedSelector
     * @param PersonFacadeSettingsSelector       $personFacadeSettingsSelector
     */
    public function __construct(
        PersonFacadeCachedSelector $personFacadeCachedSelector,
        PersonFacadeSelector $personFacadeSelector,
        PersonFacadeSettingsCachedSelector $personFacadeSettingsCachedSelector,
        PersonFacadeSettingsSelector $personFacadeSettingsSelector
    ) {
        $this->personFacadeCachedSelector = $personFacadeCachedSelector;
        $this->personFacadeSelector = $personFacadeSelector;
        $this->personFacadeSettingsCachedSelector = $personFacadeSettingsCachedSelector;
        $this->personFacadeSettingsSelector = $personFacadeSettingsSelector;
    }

    public function __destruct()
    {
        $this->personFacadeSelector = null;
        $this->personFacadeSettingsSelector = null;

        $this->personFacadeCachedSelector = null;
        $this->personFacadeSettingsCachedSelector = null;
    }

    /**
     * @return PersonFacadeSelector
     */
    public function getManager()
    {
        return $this->personFacadeSelector;
    }

    /**
     * @return PersonFacadeCachedSelector
     */
    public function getCachedManager()
    {
        return $this->personFacadeCachedSelector;
    }

    /**
     * @return PersonFacadeSettingsSelector
     */
    public function getSettingsManager()
    {
        return $this->personFacadeSettingsSelector;
    }

    /**
     * @return PersonFacadeSettingsCachedSelector
     */
    public function getSettingsCachedManager()
    {
        return $this->personFacadeSettingsCachedSelector;
    }
}
