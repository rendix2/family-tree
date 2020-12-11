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
    use GetIds;

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
     * @return TownEntity[]
     */
    public function getAll()
    {
        $towns = $this->townManager->getAll();

        $countryIds = $this->getIds($towns, '_countryId');
        $countries = $this->countryManager->getByPrimaryKeys($countryIds);

        return $this->join($towns, $countries);
    }

    /**
     * @return TownEntity[]
     */
    public function getAllCached()
    {
        return $this->cache->call([$this, 'getAll']);
    }

    /**
     * @param int $townId
     *
     * @return TownEntity
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
     * @param int $townId
     *
     * @return TownEntity
     */
    public function getByPrimaryKeyCached($townId)
    {
        return $this->cache->call([$this, 'getByPrimaryKey'], $townId);
    }


    /**
     * @param array $townIds
     *
     * @return TownEntity[]
     */
    public function getByPrimaryKeys($townIds)
    {
        $towns = $this->townManager->getByPrimaryKeys($townIds);

        if (!$towns) {
            return [];
        }

        $countryIds = $this->getIds($towns, '_countryId');

        $countries = $this->countryManager->getByPrimaryKeys($countryIds);

        return $this->join($towns, $countries);
    }

    /**
     * @param array $townIds
     *
     * @return TownEntity[]
     */
    public function getByPrimaryKeysCached($townIds)
    {
        return $this->cache->call([$this, 'getByPrimaryKeys'], $townIds);
    }

    /**
     * @param int $countryId
     *
     * @return TownEntity[]
     */
    public function getByCountryId($countryId)
    {
        $towns = $this->townManager->getAllByCountry($countryId);
        $countries = $this->countryManager->getAllCached();

        return $this->join($towns, $countries);
    }

    /**
     * @param int $countryId
     *
     * @return TownEntity[]
     */
    public function getByCountryIdCached($countryId)
    {
        return $this->cache->call([$this, 'getByCountryId'], $countryId);
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
