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

/**
 * Class TownFacadeSettingsSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Facades\Town
 */
class TownFacadeSettingsSelector extends TownFacadeSelector
{

    public function getByPrimaryKey($id)
    {
        $town = $this->getTownManager()->select()->getManager()->getByPrimaryKey($id);

        if (!$town) {
            return null;
        }

        $country = $this->getCountryManager()->select()->getManager()->getByPrimaryKey($town->_countryId);

        return $this->join([$town], [$country])[0];
    }

    public function getByPrimaryKeys(array $ids)
    {
        $towns = $this->getTownManager()->select()->getManager()->getByPrimaryKeys($ids);

        if (!$towns) {
            return [];
        }

        $countryIds = $this->getIds($towns, '_countryId');

        $countries = $this->getCountryManager()->select()->getManager()->getByPrimaryKeys($countryIds);

        return $this->join($towns, $countries);
    }

    public function getAll()
    {
        $towns = $this->getTownManager()->select()->getCachedManager()->getAll();

        $countryIds = $this->getIds($towns, '_countryId');
        $countries = $this->getCountryManager()->select()->getManager()->getByPrimaryKeys($countryIds);

        return $this->join($towns, $countries);
    }

    public function getAllByCountry($countryId)
    {
        $towns = $this->getTownManager()->select()->getManager()->getAllByCountry($countryId);
        $countries = $this->getCountryManager()->select()->getCachedManager()->getAll();

        return $this->join($towns, $countries);
    }

    public function getToMap()
    {
        $towns = $this->getTownManager()->select()->getManager()->getToMap();
        $countries = $this->getCountryManager()->select()->getCachedManager()->getAll();

        return $this->join($towns, $countries);
    }
}
