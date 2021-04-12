<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownFacadeSelector.php
 * User: Tomáš Babický
 * Date: 10.04.2021
 * Time: 14:48
 */

namespace Rendix2\FamilyTree\App\Model\Facades\Town;

use Dibi\Fluent;
use Nette\NotImplementedException;
use Rendix2\FamilyTree\App\Filters\TownFilter;
use Rendix2\FamilyTree\App\Model\Entities\CountryEntity;
use Rendix2\FamilyTree\App\Model\Entities\TownEntity;
use Rendix2\FamilyTree\App\Model\Facades\DefaultFacade\DefaultFacadeSelector;
use Rendix2\FamilyTree\App\Model\Managers\CountryManager;
use Rendix2\FamilyTree\App\Model\Managers\Town\Interfaces\ITownSelector;
use Rendix2\FamilyTree\App\Model\Managers\TownManager;

/**
 * Class TownFacadeSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Facades\Town
 */
class TownFacadeSelector extends DefaultFacadeSelector implements ITownSelector
{
    /**
     * @var CountryManager $countryManager
     */
    private $countryManager;

    /**
     * @var TownManager $townManager
     */
    private $townManager;

    /**
     * TownFacade constructor.
     *
     * @param CountryManager $countryManager
     * @param TownFilter     $townFilter
     * @param TownManager    $townManager
     */
    public function __construct(
        CountryManager $countryManager,
        TownFilter $townFilter,
        TownManager $townManager
    ) {
        parent::__construct($townFilter);

        $this->countryManager = $countryManager;
        $this->townManager = $townManager;
    }

    /**
     * @return CountryManager
     */
    public function getCountryManager()
    {
        return $this->countryManager;
    }

    /**
     * @return TownManager
     */
    public function getTownManager()
    {
        return $this->townManager;
    }

    /**
     * @param TownEntity[] $towns
     * @param CountryEntity[] $countries
     *
     * @return TownEntity[]
     */
    public function join(array $towns, array $countries)
    {
        foreach ($towns as $town) {
            foreach ($countries as $country) {
                if ($country->id === $town->_countryId) {
                    $town->country = $country;
                    break;
                }
            }

            $town->clean();
        }

        return $towns;
    }

    /**
     * @param int $id
     *
     * @return TownEntity
     */
    public function getByPrimaryKey($id)
    {
        $town = $this->townManager->select()->getManager()->getByPrimaryKey($id);

        if (!$town) {
            return null;
        }

        $country = $this->countryManager->select()->getManager()->getByPrimaryKey($town->_countryId);

        return $this->join([$town], [$country])[0];
    }

    /**
     * @param array $ids
     *
     * @return TownEntity[]
     */
    public function getByPrimaryKeys(array $ids)
    {
        $towns = $this->townManager->select()->getManager()->getByPrimaryKeys($ids);

        if (!$towns) {
            return [];
        }

        $countryIds = $this->getIds($towns, '_countryId');

        $countries = $this->countryManager->select()->getManager()->getByPrimaryKeys($countryIds);

        return $this->join($towns, $countries);
    }

    public function getColumnFluent($column)
    {
        throw new NotImplementedException();
    }

    /**
     * @return TownEntity[]
     */
    public function getAll()
    {
        $towns = $this->townManager->select()->getCachedManager()->getAll();

        $countryIds = $this->getIds($towns, '_countryId');
        $countries = $this->countryManager->select()->getManager()->getByPrimaryKeys($countryIds);

        return $this->join($towns, $countries);
    }

    public function getPairs($column)
    {
        throw new NotImplementedException();
    }

    public function getBySubQuery(Fluent $query)
    {
        throw new NotImplementedException();
    }

    public function getPairsByCountry($countryId)
    {
        throw new NotImplementedException();
    }

    public function getAllPairs()
    {
        throw new NotImplementedException();
    }

    /**
     * @param int $countryId
     *
     * @return TownEntity[]
     */
    public function getAllByCountry($countryId)
    {
        $towns = $this->townManager->select()->getManager()->getAllByCountry($countryId);
        $countries = $this->countryManager->select()->getCachedManager()->getAll();

        return $this->join($towns, $countries);
    }

    public function getToMap()
    {
        $towns = $this->townManager->select()->getManager()->getToMap();
        $countries = $this->countryManager->select()->getCachedManager()->getAll();

        return $this->join($towns, $countries);
    }
}