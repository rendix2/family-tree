<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownFacade.php
 * User: Tomáš Babický
 * Date: 10.11.2020
 * Time: 2:51
 */

namespace Rendix2\FamilyTree\App\Model\Facades;

use http\Env\Request;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Managers\CountryManager;
use Rendix2\FamilyTree\App\Managers\TownManager;
use Rendix2\FamilyTree\App\Model\Entities\CountryEntity;
use Rendix2\FamilyTree\App\Model\Entities\TownEntity;

/**
 * Class TownFacade
 *
 * @package Rendix2\FamilyTree\App\Facades
 */
class TownFacade
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
     * @var Cache $cache
     */
    private $cache;

    /**
     * TownFacade constructor.
     *
     * @param IStorage $storage
     * @param CountryManager $countryManager
     * @param TownManager $townManager
     */
    public function __construct(
        IStorage $storage,
        CountryManager $countryManager,
        TownManager $townManager
    ) {
        $this->cache = new Cache($storage, self::class);
        $this->countryManager = $countryManager;
        $this->townManager = $townManager;
    }

    /**
     * @param TownEntity[] $towns
     * @param CountryEntity[] $countries
     * @return array
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
        }

        return $towns;
    }

    /**
     * @return TownEntity[]
     */
    public function getAll()
    {
        $towns = $this->townManager->getAll();
        $countries = $this->countryManager->getAll();

        return $this->join($towns, $countries);
    }

    /**
     * @return TownEntity[]
     */
    public function getAllCached()
    {
        $towns = $this->townManager->getAllCached();
        $countries = $this->countryManager->getAllCached();

        return $this->cache->call([$this, 'join'], $towns, $countries);
    }

    /**
     * @param int $townId
     * @return TownEntity|null
     */
    public function getByPrimaryKey($townId)
    {
        $town = $this->townManager->getByPrimaryKey($townId);

        if (!$town) {
            return null;
        }

        $country = $this->countryManager->getByPrimaryKey($town->_countryId);
        $town->country = $country;

        return $town;
    }

    /**
     * @return TownEntity[]
     */
    public function getToMap()
    {
        $towns = $this->townManager->getToMap();
        $countries = $this->countryManager->getAllCached();

        return $this->join($towns, $countries);
    }
}
