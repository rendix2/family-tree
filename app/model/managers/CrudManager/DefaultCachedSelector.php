<?php
/**
 *
 * Created by PhpStorm.
 * Filename: DefaultCachedSelector.php
 * User: Tomáš Babický
 * Date: 03.04.2021
 * Time: 1:03
 */

namespace Rendix2\FamilyTree\App\Model\CrudManager;

use Dibi\Fluent;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Model\Interfaces\ICachedSelector;

/**
 * Class DefaultCachedSelector
 *
 * @package Rendix2\FamilyTree\App\Model
 */
class DefaultCachedSelector implements ICachedSelector
{
    /**
     * @var Cache $cache
     */
    private $cache;

    /**
     * @var DefaultSelector $selector
     */
    private $selector;

    /**
     * DefaultCachedSelector constructor.
     *
     * @param IStorage        $storage
     * @param DefaultSelector $selector
     */
    public function __construct(
        IStorage $storage,
        DefaultSelector $selector
    ) {
        $cacheName = static::class . '::' . $selector->getTable()->getTableName();

        $this->cache = new Cache($storage, $cacheName);
        $this->selector = $selector;
    }

    /**
     * @return Cache
     */
    protected function getCache()
    {
        return $this->cache;
    }

    /**
     * @return DefaultSelector
     */
    protected function getSelector()
    {
        return $this->selector;
    }

    public function getByPrimaryKey($id)
    {
        return $this->cache->call([$this->selector, 'getByPrimaryKey'], $id);
    }

    public function getAll()
    {
        return $this->cache->call([$this->selector, 'getAll']);
    }

    public function getByPrimaryKeys(array $ids)
    {
        return $this->cache->call([$this->selector, 'getByPrimaryKeys'], $ids);
    }

    public function getPairs($column)
    {
        return $this->cache->call([$this->selector, 'getPairs'], $column);
    }

    public function getBySubQuery(Fluent $query)
    {
        return $this->cache->call([$this->selector, 'getBySubQuery'], $query);
    }

    public function getColumnFluent($column)
    {
        return $this->cache->call([$this->selector, 'getColumnFluent'], $column);
    }

    public function getAllPairs()
    {
        return $this->cache->call([$this->selector, 'getAllPairs']);
    }
}
