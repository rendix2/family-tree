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
use Rendix2\FamilyTree\App\Model\Entities\PersonEntity;

/**
 * Class PersonSettingsFacade
 *
 * @package Rendix2\FamilyTree\App\Model\Facades
 */
class PersonSettingsFacade extends PersonFacade
{
    /**
     * @var PersonSettingsManager $personSettingsManager
     */
    private $personSettingsManager;

    /**
     * @var TownFacade $townFacade
     */
    private $townFacade;

    /**
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * @var GenusManager $genusManager
     */
    private $genusManager;

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

        $this->personSettingsManager = $personSettingsManager;
        $this->townFacade = $townFacade;
        $this->addressFacade = $addressFacade;
        $this->genusManager = $genusManager;
    }

    /**
     * @return PersonEntity[]
     */
    public function getAll()
    {
        $persons = $this->personSettingsManager->getAll();

        $towns = $this->townFacade->getAll();
        $addresses = $this->addressFacade->getAll();
        $genuses = $this->genusManager->getAll();

        return $this->join($persons, $persons, $towns, $addresses, $genuses);
    }
}
