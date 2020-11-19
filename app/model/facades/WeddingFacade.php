<?php
/**
 *
 * Created by PhpStorm.
 * Filename: WeddigFacade.php
 * User: Tomáš Babický
 * Date: 10.11.2020
 * Time: 2:44
 */

namespace Rendix2\FamilyTree\App\Facades;

use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Managers\GenusManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\TownManager;
use Rendix2\FamilyTree\App\Managers\WeddingManager;
use Rendix2\FamilyTree\App\Model\Entities\DurationEntity;
use Rendix2\FamilyTree\App\Model\Entities\PersonEntity;
use Rendix2\FamilyTree\App\Model\Entities\TownEntity;
use Rendix2\FamilyTree\App\Model\Entities\WeddingEntity;
use Rendix2\FamilyTree\App\Model\Facades\TownFacade;

/**
 * Class WeddingFacade
 *
 * @package Rendix2\FamilyTree\App\Facades
 */
class WeddingFacade
{
    /**
     * @var Cache $cache
     */
    private $cache;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var WeddingManager $weddingManager
     */
    private $weddingManager;

    /**
     * @var TownFacade $townFacade
     */
    private $townFacade;

    /**
     * WeddingFacade constructor.
     *
     * @param IStorage $storage
     * @param PersonManager $personManager
     * @param TownFacade $townFacade
     * @param WeddingManager $weddingManager
     */
    public function __construct(
        IStorage $storage,
        PersonManager $personManager,
        TownFacade $townFacade,
        WeddingManager $weddingManager
    )
    {
        $this->cache = new Cache($storage, self::class);
        $this->personManager = $personManager;
        $this->townFacade = $townFacade;
        $this->weddingManager = $weddingManager;
    }

    /**
     * @param WeddingEntity[] $weddings
     * @param PersonEntity[] $persons
     * @param TownEntity[] $towns
     *
     * @return WeddingEntity[]
     */
    public function join(array $weddings, array $persons, array $towns)
    {
        foreach ($weddings as $wedding) {
            foreach ($persons as $person) {
                if ($wedding->_husbandId === $person->id) {
                    $wedding->husband = $person;
                    break;
                }
            }

            foreach ($persons as $person) {
                if ($wedding->_wifeId === $person->id) {
                    $wedding->wife = $person;
                    break;
                }
            }

            foreach ($towns as $town) {
                if ($wedding->_townId === $town->id) {
                    $wedding->town = $town;
                    break;
                }
            }

            $durationEntity = new DurationEntity((array)$wedding);
            $wedding->duration = $durationEntity;

            $wedding->clean();
        }

        return $weddings;
    }

    /**
     * @return WeddingEntity[]
     */
    public function getAll()
    {
        $weddings = $this->weddingManager->getAll();
        $persons = $this->personManager->getAll();
        $towns = $this->townFacade->getAll();

        return $this->join($weddings, $persons, $towns);
    }

    /**
     * @return WeddingEntity[]
     */
    public function getAllCached()
    {
        $weddings = $this->weddingManager->getAllCached();
        $persons = $this->personManager->getAllCached();
        $towns = $this->townFacade->getAllCached();

        return $this->cache->call([$this, 'join'], $weddings, $persons, $towns);
    }

    /**
     * @param int $weddingId
     *
     * @return WeddingEntity
     */
    public function getByPrimaryKey($weddingId)
    {
        $wedding = $this->weddingManager->getByPrimaryKey($weddingId);

        if (!$wedding) {
            return null;
        }

        $persons = $this->personManager->getAll();
        $towns = $this->townFacade->getAll();

        return $this->join([$wedding], $persons, $towns)[0];
    }

    /**
     * @param int $weddingId
     *
     * @return WeddingEntity
     */
    public function getByPrimaryKeyCached($weddingId)
    {
        $wedding = $this->weddingManager->getByPrimaryKeyCached($weddingId);

        if (!$wedding) {
            return null;
        }

        $persons = $this->personManager->getAllCached();
        $towns = $this->townFacade->getAllCached();

        return $this->join([$wedding], $persons, $towns)[0];
    }

    /**
     * @param int $wifeId
     *
     * @return WeddingEntity[]
     */
    public function getByWife($wifeId)
    {
        $weddings = $this->weddingManager->getAllByWifeId($wifeId);

        if (!$weddings) {
            return [];
        }

        $persons = $this->personManager->getAll();
        $towns = $this->townFacade->getAll();

        return $this->join($weddings, $persons, $towns);
    }

    /**
     * @param int $wifeId
     *
     * @return WeddingEntity[]
     */
    public function getByWifeCached($wifeId)
    {
        return $this->cache->call([$this, 'getByWife'], $wifeId);
    }

    /**
     * @param int $husbandId
     *
     * @return WeddingEntity[]
     */
    public function getByHusband($husbandId)
    {
        $weddings = $this->weddingManager->getAllByHusbandId($husbandId);

        if (!$weddings) {
            return [];
        }

        $persons = $this->personManager->getAll();
        $towns = $this->townFacade->getAll();

        return $this->join($weddings, $persons, $towns);
    }

    /**
     * @param int $husbandId
     *
     * @return WeddingEntity[]
     */
    public function getByHusbandCached($husbandId)
    {
        return $this->cache->call([$this, 'getByHusband'], $husbandId);
    }

    /**
     * @param int $townId
     *
     * @return WeddingEntity[]
     */
    public function getByTown($townId)
    {
        $weddings = $this->weddingManager->getByTownId($townId);

        if (!$weddings) {
            return [];
        }

        $persons = $this->personManager->getAll();
        $towns = $this->townFacade->getAll();

        return $this->join($weddings, $persons, $towns);
    }

    /**
     * @param int $townId
     *
     * @return WeddingEntity[]
     */
    public function getByTownIdCached($townId)
    {
        return $this->cache->call([$this, 'getByTown'], $townId);
    }
}
