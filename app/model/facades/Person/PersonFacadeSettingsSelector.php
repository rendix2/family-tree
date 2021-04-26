<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonFacadeSettingsSelector.php
 * User: Tomáš Babický
 * Date: 10.04.2021
 * Time: 2:47
 */

namespace Rendix2\FamilyTree\App\Model\Facades\Person;

use Dibi\Connection;
use Dibi\Fluent;
use Nette\Http\IRequest;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Model\Facades\TownFacade;
use Rendix2\FamilyTree\App\Model\Managers\Address\AddressTable;
use Rendix2\FamilyTree\App\Model\Managers\Country\CountryTable;
use Rendix2\FamilyTree\App\Model\Managers\Genus\GenusTable;
use Rendix2\FamilyTree\App\Model\Managers\GenusManager;
use Rendix2\FamilyTree\App\Model\Managers\Person\PersonTable;
use Rendix2\FamilyTree\App\Model\Managers\PersonManager;
use Rendix2\FamilyTree\App\Model\Managers\Town\TownTable;
use Rendix2\FamilyTree\SettingsModule\App\Presenters\TownPresenter;

class PersonFacadeSettingsSelector extends PersonFacadeSelector
{
    /**
     * @var IRequest $request
     */
    private $request;

    public function __construct(
        IRequest $request,
        AddressTable $addressTable,
        AddressFacade $addressFacade,
        Connection $connection,
        CountryTable $countryTable,
        GenusTable $genusTable,
        GenusManager $genusManager,
        PersonFilter $personFilter,
        PersonManager $personManager,
        PersonTable $personTable,
        TownTable $townTable,
        TownFacade $townFacade
    ) {
        parent::__construct(
            $addressTable,
            $addressFacade,
            $connection,
            $countryTable,
            $genusTable,
            $genusManager,
            $personFilter,
            $personManager,
            $personTable,
            $townTable,
            $townFacade
        );

        $this->request = $request;
    }

    /**
     * @return Fluent
     */
    public function getAllFluent()
    {
        $fluent = parent::getAllFluent();

        $setting = (int)$this->request->getCookie(TownPresenter::TOWN_ORDERING);
        $orderWay = (int)$this->request->getCookie(TownPresenter::TOWN_ORDERING_WAY);

        if ($setting === TownPresenter::TOWN_ORDERING_ID) {
            return $fluent
                ->orderBy('p.' . $this->getPersonTable()->getPrimaryKey(), $orderWay);
        } elseif ($setting === TownPresenter::TOWN_ORDERING_NAME) {
            return $fluent
                ->orderBy('p.name', $orderWay);
        } elseif ($setting === TownPresenter::TOWN_ORDERING_ZIP) {
            return $fluent
                ->orderBy('p.zip', $orderWay);
        } elseif ($setting === TownPresenter::TOWN_ORDERING_NAME_ZIP) {
            return $fluent
                ->orderBy('p.name', $orderWay)
                ->orderBy('p.zip', $orderWay);
        } elseif ($setting === TownPresenter::TOWN_ORDERING_ZIP_NAME) {
            return $fluent
                ->orderBy('p.zip', $orderWay)
                ->orderBy('p.name', $orderWay);
        } else {
            return $fluent
                ->orderBy('p.' . $this->getPersonTable()->getPrimaryKey());
        }
    }
}
