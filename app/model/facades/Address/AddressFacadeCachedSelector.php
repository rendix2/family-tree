<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressCachedSelector.php
 * User: Tomáš Babický
 * Date: 05.04.2021
 * Time: 17:12
 */

namespace Rendix2\FamilyTree\App\Model\Facades\Address;

use Dibi\Fluent;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\NotImplementedException;
use Rendix2\FamilyTree\App\Model\Managers\Address\Interfaces\IAddressSelector;

class AddressFacadeCachedSelector implements IAddressSelector
{
    /**
     * @var AddressFacadeSelector $selector
     */
    private $selector;

    /**
     * @var Cache $cache
     */
    private $cache;

    /**
     * AddressFacadeCachedSelector constructor.
     *
     * @param AddressFacadeSelector $addressFacadeSelector
     * @param IStorage              $storage
     */
    public function __construct(
        AddressFacadeSelector $addressFacadeSelector,
        IStorage $storage
    ) {
        $this->selector = $addressFacadeSelector;
        $this->cache = new Cache($storage, static::class);
    }

    public function getByCountryId($countryId)
    {
        return $this->cache->call([$this->selector, 'getByCountryId'], $countryId);
    }

    public function getByTownId($townId)
    {
        return $this->cache->call([$this->selector, 'getByTownId'], $townId);
    }

    public function getToMap()
    {
        return $this->cache->call([$this->selector, 'getToMap']);
    }

    public function getByPrimaryKey($id)
    {
        return $this->cache->call([$this->selector, 'getByPrimaryKey'], $id);
    }

    public function getByPrimaryKeys(array $ids)
    {
        return $this->cache->call([$this->selector, 'getByPrimaryKeys'], $ids);
    }

    public function getColumnFluent($column)
    {
        throw new NotImplementedException();
    }

    public function getAll()
    {
        return $this->cache->call([$this->selector, 'getAll']);
    }

    public function getPairs($column)
    {
        throw new NotImplementedException();
    }

    public function getBySubQuery(Fluent $query)
    {
        return $this->cache->call([$this->selector, 'getBySubQuery'], $query);
    }

    public function getByTownPairs($townId)
    {
        return $this->cache->call([$this->selector, 'getByTownPairs'], $townId);
    }

    public function getAllPairs()
    {
        return $this->cache->call([$this->selector, 'getAllPairs']);
    }
}
