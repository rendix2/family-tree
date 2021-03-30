<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonSettingsFacade.php
 * User: Tomáš Babický
 * Date: 11.02.2021
 * Time: 14:50
 */

namespace Rendix2\FamilyTree\App\Model\Facades;

use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Managers\GenusManager;
use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;

/**
 * Class PersonSettingsFacade
 *
 * @package Rendix2\FamilyTree\App\Model\Facades
 */
class PersonSettingsFacade extends PersonFacade
{
    /**
     * PersonSettingsFacade constructor.
     *
     * @param AddressFacade $addressFacade
     * @param IStorage $storage
     * @param GenusManager $genusManager
     * @param PersonSettingsManager $personSettingsManager
     * @param TownFacade $townFacade
     */
    public function __construct(
        AddressFacade $addressFacade,
        IStorage $storage,
        GenusManager $genusManager,
        PersonSettingsManager $personSettingsManager,
        TownFacade $townFacade
    ) {
        parent::__construct($addressFacade, $storage, $genusManager, $personSettingsManager, $townFacade);
    }
}
