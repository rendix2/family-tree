<?php
/**
 *
 * Created by PhpStorm.
 * Filename: HistoryNoteCachedSelector.php
 * User: Tomáš Babický
 * Date: 08.04.2021
 * Time: 2:20
 */

namespace Rendix2\FamilyTree\App\Model\Facades\HistoryNote;


use Dibi\Fluent;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\NotImplementedException;
use Rendix2\FamilyTree\App\Model\Managers\HistoryNote\Interfaces\IHistoryNoteSelector;

class HistoryNoteFacadeCachedSelector implements IHistoryNoteSelector
{
    /**
     * @var Cache $cache
     */
    private $cache;

    /**
     * @var HistoryNoteFacadeSelector $selector
     */
    private $selector;

    /**
     * HistoryNoteFacadeCachedSelector constructor.
     *
     * @param IStorage                  $storage
     * @param HistoryNoteFacadeSelector $selector
     */
    public function __construct(IStorage $storage, HistoryNoteFacadeSelector $selector)
    {
        $this->cache = new Cache($storage, static::class);
        $this->selector = $selector;
    }

    public function getByPersonId($personId)
    {
        return $this->cache->call([$this->selector, 'getByPersonId'], $personId);
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
