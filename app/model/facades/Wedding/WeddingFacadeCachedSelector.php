<?php
/**
 *
 * Created by PhpStorm.
 * Filename: WeddingFacadeCachedSelector.php
 * User: Tomáš Babický
 * Date: 12.04.2021
 * Time: 2:09
 */

namespace Rendix2\FamilyTree\App\Model\Facades\Wedding;

use Dibi\Fluent;
use Dibi\Row;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\NotImplementedException;
use Rendix2\FamilyTree\App\Model\Entities\WeddingEntity;
use Rendix2\FamilyTree\App\Model\Managers\Wedding\Interfaces\IWeddingSelector;

class WeddingFacadeCachedSelector implements IWeddingSelector
{
    /**
     * @var Cache $cache
     */
    private $cache;

    /**
     * @var WeddingFacadeSelector $selector
     */
    private $selector;

    /**
     * WeddingFacadeCachedSelector constructor.
     *
     * @param IStorage              $storage
     * @param WeddingFacadeSelector $selector
     */
    public function __construct(
        IStorage $storage,
        WeddingFacadeSelector $selector
    ) {
        $this->cache = new Cache($storage, static::class);
        $this->selector = $selector;
    }

    public function getByPrimaryKey($id)
    {
        return $this->cache->call([$this->selector, 'getByPrimaryKey'], $id);
    }

    public function getByPrimaryKeys(array $ids)
    {
        throw new NotImplementedException();
    }

    public function getColumnFluent($column)
    {
        throw new NotImplementedException();
    }

    public function getAll()
    {
        return $this->cache->call([$this->selector, 'getAll']);
    }

    public function getAllPairs()
    {
        throw new NotImplementedException();
    }

    public function getPairs($column)
    {
        throw new NotImplementedException();
    }

    public function getBySubQuery(Fluent $query)
    {
        throw new NotImplementedException();
    }

    public function getAllByHusbandId($husbandId)
    {
        return $this->cache->call([$this->selector, 'getAllByHusbandId'], $husbandId);
    }

    public function getAllByWifeId($wifeId)
    {
        return $this->cache->call([$this->selector, 'getAllByWifeId'], $wifeId);
    }

    public function getByWifeIdAndHusbandId($wifeId, $husbandId)
    {
        return $this->cache->call([$this->selector, 'getByWifeIdAndHusbandId'], $wifeId, $husbandId);
    }

    public function getByTownId($townId)
    {
        return $this->cache->call([$this->selector, 'getByTownId'], $townId);
    }

    public function getByAddressId($addressId)
    {
        return $this->cache->call([$this->selector, 'getByAddressId'], $addressId);
    }
}
