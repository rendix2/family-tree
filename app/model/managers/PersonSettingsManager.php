<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonSettinsManager.php
 * User: Tomáš Babický
 * Date: 11.02.2021
 * Time: 14:48
 */

namespace Rendix2\FamilyTree\App\Managers;

use Dibi\Fluent;
use Rendix2\FamilyTree\SettingsModule\App\Presenters\PersonPresenter;

/**
 * Class PersonSettingsManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class PersonSettingsManager extends PersonManager
{
    /**
     * @return Fluent
     */
    public function getAllFluent()
    {
        $setting = (int)$this->getRequest()->getCookie(PersonPresenter::PERSON_ORDERING);
        $orderWay = $this->getRequest()->getCookie(PersonPresenter::PERSON_ORDERING_WAY);

        if ($setting === PersonPresenter::PERSON_ORDERING_ID) {
            return parent::getAllFluent()
                ->orderBy($this->getPrimaryKey(), $orderWay);
        } elseif ($setting === PersonPresenter::PERSON_ORDERING_NAME) {
            return parent::getAllFluent()
                ->orderBy('name', $orderWay);
        } elseif ($setting === PersonPresenter::PERSON_ORDERING_SURNAME) {
            return parent::getAllFluent()
                ->orderBy('surname', $orderWay);
        } elseif ($setting === PersonPresenter::PERSON_ORDERING_NAME_SURNAME) {
            return parent::getAllFluent()
                ->orderBy('name', $orderWay)
                ->orderBy('surname', $orderWay);
        } elseif ($setting === PersonPresenter::PERSON_ORDERING_SURNAME_NAME) {
            return parent::getAllFluent()
                ->orderBy('surname', $orderWay)
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
        return Tables::PERSON_TABLE;
    }
}
