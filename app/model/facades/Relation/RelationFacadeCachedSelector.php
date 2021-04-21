<?php
/**
 *
 * Created by PhpStorm.
 * Filename: RelationCachedSelector.php
 * User: Tomáš Babický
 * Date: 10.04.2021
 * Time: 14:19
 */

namespace Rendix2\FamilyTree\App\Model\Facades\Relation;

use Dibi\Fluent;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\NotImplementedException;
use Rendix2\FamilyTree\App\Model\Managers\Relation\IRelationSelector;

class RelationFacadeCachedSelector implements IRelationSelector
{
    /**
     * @var Cache $cache
     */
    private $cache;

    /**
     * @var RelationFacadeSelector $selector
     */
    private $selector;

    /**
     * RelationFacadeCachedSelector constructor.
     *
     * @param IStorage               $storage
     * @param RelationFacadeSelector $relationFacadeSelector
     */
    public function __construct(
        IStorage $storage,
        RelationFacadeSelector $relationFacadeSelector
    ) {
        $this->cache = new Cache($storage, static::class);
        $this->selector = $relationFacadeSelector;
    }

    public function __destruct()
    {
        $this->cache = null;
        $this->selector = null;
    }

    public function getByMaleId($maleId)
    {
        return $this->cache->call([$this->selector, 'getByMaleId'], $maleId);
    }

    public function getByFemaleId($femaleId)
    {
        return $this->cache->call([$this->selector, 'getByFemaleId'], $femaleId);
    }

    public function getByMaleIdAndFemaleId($maleId, $femaleId)
    {
        return $this->cache->call([$this->selector, 'getByMaleIdAndFemaleId'], $maleId, $femaleId);
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
}