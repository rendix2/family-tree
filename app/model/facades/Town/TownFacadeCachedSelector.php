<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownFacadeCachedSelector.php
 * User: Tomáš Babický
 * Date: 10.04.2021
 * Time: 14:48
 */

namespace Rendix2\FamilyTree\App\Model\Facades\Town;

use Dibi\Fluent;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\NotImplementedException;
use Rendix2\FamilyTree\App\Model\Managers\Town\Interfaces\ITownSelector;

/**
 * Class TownFacadeCachedSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Facades\Town
 */
class TownFacadeCachedSelector implements ITownSelector
{
    /**
     * @var Cache $cache
     */
    private $cache;

    /**
     * @var TownFacadeSelector $selector
     */
    private $selector;

    /**
     * TownFacadeCachedSelector constructor.
     *
     * @param IStorage           $storage
     * @param TownFacadeSelector $selector
     */
    public function __construct(
        IStorage $storage,
        TownFacadeSelector $selector
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

    public function getAllByCountry($countryId)
    {
        return $this->cache->call([$this->selector, 'getByCountryId'], $countryId);
    }

    public function getToMap()
    {
        return $this->cache->call([$this->selector, 'getToMap']);
    }
}