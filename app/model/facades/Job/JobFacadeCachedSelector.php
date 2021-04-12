<?php
/**
 *
 * Created by PhpStorm.
 * Filename: JobFacadeCachedSelector.php
 * User: Tomáš Babický
 * Date: 09.04.2021
 * Time: 14:05
 */

namespace Rendix2\FamilyTree\App\Model\Facades\Job;

use Dibi\Fluent;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\NotImplementedException;
use Rendix2\FamilyTree\App\Model\Managers\Job\Interfaces\IJobSelector;

/**
 * Class JobFacadeCachedSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Facades\Job
 */
class JobFacadeCachedSelector implements IJobSelector
{
    /**
     * @var Cache $cache
     */
    private $cache;

    /**
     * @var JobFacadeSelector $selector
     */
    private $selector;

    /**
     * JobFacadeCachedSelector constructor.
     *
     * @param IStorage          $storage
     * @param JobFacadeSelector $selector
     */
    public function __construct(
        IStorage $storage,
        JobFacadeSelector $selector
    ) {
        $this->cache = new Cache($storage, static::class);
        $this->selector = $selector;
    }

    public function getByTownId($townId)
    {
        return $this->cache->call([$this->selector, 'getByTownId'], $townId);
    }

    public function getByAddressId($addressId)
    {
        return $this->cache->call([$this->selector, 'getByAddressId'], $addressId);
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

    public function getAllPairs()
    {
        return $this->cache->call([$this->selector, 'getAllPairs']);
    }

    public function getPairs($column)
    {
        throw new NotImplementedException();
    }

    public function getBySubQuery(Fluent $query)
    {
        return $this->cache->call([$this->selector, 'getBySubQuery'], $query);
    }
}
