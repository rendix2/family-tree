<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TonwFacadeSettingsSelector.php
 * User: Tomáš Babický
 * Date: 10.04.2021
 * Time: 14:49
 */

namespace Rendix2\FamilyTree\App\Model\Facades\Town;

use Dibi\Connection;
use Nette\Http\IRequest;
use Rendix2\FamilyTree\App\Filters\TownFilter;
use Rendix2\FamilyTree\App\Model\Managers\Country\CountryTable;
use Rendix2\FamilyTree\App\Model\Managers\Town\TownTable;
use Rendix2\FamilyTree\SettingsModule\App\Presenters\TownPresenter;

/**
 * Class TownFacadeSettingsSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Facades\Town
 */
class TownFacadeSettingsSelector extends TownFacadeSelector
{
    /**
     * @var IRequest $request
     */
    private $request;

    /**
     * TownFacadeSettingsSelector constructor.
     *
     * @param IRequest     $request
     * @param Connection   $connection
     * @param CountryTable $countryTable
     * @param TownFilter   $townFilter
     * @param TownTable    $townTable
     */
    public function __construct(
        IRequest $request,
        Connection $connection,
        CountryTable $countryTable,
        TownFilter $townFilter,
        TownTable $townTable
    ) {
        parent::__construct($connection, $countryTable, $townFilter, $townTable);

        $this->request = $request;
    }

    public function getAllFluent()
    {
        $fluent = parent::getAllFluent();

        $setting = (int)$this->request->getCookie(TownPresenter::TOWN_ORDERING);
        $orderWay = (int)$this->request->getCookie(TownPresenter::TOWN_ORDERING_WAY);

        if ($setting === TownPresenter::TOWN_ORDERING_ID) {
            return $fluent
                ->orderBy('t.' . $this->getTownTable()->getPrimaryKey(), $orderWay);
        } elseif ($setting === TownPresenter::TOWN_ORDERING_NAME) {
            return $fluent
                ->orderBy('t.name', $orderWay);
        } elseif ($setting === TownPresenter::TOWN_ORDERING_ZIP) {
            return $fluent
                ->orderBy('t.zip', $orderWay);
        } elseif ($setting === TownPresenter::TOWN_ORDERING_NAME_ZIP) {
            return $fluent
                ->orderBy('t.name', $orderWay)
                ->orderBy('t.zip', $orderWay);
        } elseif ($setting === TownPresenter::TOWN_ORDERING_ZIP_NAME) {
            return $fluent
                ->orderBy('t.zip', $orderWay)
                ->orderBy('t.name', $orderWay);
        } else {
            return $fluent
                ->orderBy('t.' . $this->getTownTable()->getPrimaryKey());
        }
    }
}
