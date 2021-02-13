<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownSettingsManager.php
 * User: Tomáš Babický
 * Date: 12.02.2021
 * Time: 1:01
 */

namespace Rendix2\FamilyTree\App\Managers;

use Dibi\Fluent;
use Rendix2\FamilyTree\SettingsModule\App\Presenters\TownPresenter;

/**
 * Class TownSettingsManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class TownSettingsManager extends TownManager
{
    /**
     * @return Fluent
     */
    public function getAllFluent()
    {
        $setting = (int)$this->getRequest()->getCookie(TownPresenter::TOWN_ORDERING);
        $orderWay = (int)$this->getRequest()->getCookie(TownPresenter::TOWN_ORDERING_WAY);

        if ($setting === TownPresenter::TOWN_ORDERING_ID) {
            return parent::getAllFluent()
                ->orderBy($this->getPrimaryKey(), $orderWay);
        } elseif ($setting === TownPresenter::TOWN_ORDERING_NAME) {
            return parent::getAllFluent()
                ->orderBy('name', $orderWay);
        } elseif ($setting === TownPresenter::TOWN_ORDERING_ZIP) {
            return parent::getAllFluent()
                ->orderBy('zip', $orderWay);
        } elseif ($setting === TownPresenter::TOWN_ORDERING_NAME_ZIP) {
            return parent::getAllFluent()
                ->orderBy('name', $orderWay)
                ->orderBy('zip', $orderWay);
        } elseif ($setting === TownPresenter::TOWN_ORDERING_ZIP_NAME) {
            return parent::getAllFluent()
                ->orderBy('zip', $orderWay)
                ->orderBy('name', $orderWay);
        } else {
            return parent::getAllFluent()
                ->orderBy($this->getPrimaryKey());
        }
    }

    /**
     * @return false|string
     */
    public function getClassName()
    {
        return Tables::TOWN_TABLE;
    }
}
