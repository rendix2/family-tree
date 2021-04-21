<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceCachedSelector.php
 * User: Tomáš Babický
 * Date: 06.04.2021
 * Time: 3:12
 */

namespace Rendix2\FamilyTree\App\Model\Facades\Source;

use Dibi\Fluent;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\NotImplementedException;
use Rendix2\FamilyTree\App\Model\Managers\Source\Interfaces\ISourceSelector;

/**
 * Class SourceCachedSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Facades\Source
 */
class SourceFacadeCachedSelector implements ISourceSelector
{
    /**
     * @var Cache $cache
     */
    private $cache;

    /**
     * @var SourceFacadeSelector $selector
     */
    private $selector;

    /**
     * SourceCachedSelector constructor.
     *
     * @param IStorage             $storage
     * @param SourceFacadeSelector $selector
     */
    public function __construct(
        IStorage $storage,
        SourceFacadeSelector $selector
    ) {
        $this->cache = new Cache($storage, static::class);
        $this->selector = $selector;
    }

    public function __destruct()
    {
        $this->cache = null;
        $this->selector = null;
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

    public function getPairs($column)
    {
        throw new NotImplementedException();
    }

    public function getBySubQuery(Fluent $query)
    {
        throw new NotImplementedException();
    }

    public function getByPersonId($personId)
    {
        return $this->cache->call([$this->selector, 'getByPersonId'], $personId);
    }

    public function getBySourceTypeId($sourceTypeId)
    {
        return $this->cache->call([$this->selector, 'getBySourceTypeId'], $sourceTypeId);
    }

    public function getAllPairs()
    {
        return $this->cache->call([$this->selector, 'getAllPairs']);
    }
}
