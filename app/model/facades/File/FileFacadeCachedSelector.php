<?php
/**
 *
 * Created by PhpStorm.
 * Filename: FileCachedSelector.php
 * User: Tomáš Babický
 * Date: 07.04.2021
 * Time: 0:24
 */

namespace Rendix2\FamilyTree\App\Model\Facades\File;


use Dibi\Fluent;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\NotImplementedException;
use Rendix2\FamilyTree\App\Model\Managers\File\Interfaces\IFileSelector;

class FileFacadeCachedSelector implements IFileSelector
{
    /**
     * @var Cache $cache
     */
    private $cache;

    /**
     * @var FileFacadeSelector $selector
     */
    private $selector;

    /**
     * FileFacadeCachedSelector constructor.
     *
     * @param IStorage           $storage
     * @param FileFacadeSelector $fileFacadeSelector
     */
    public function __construct(
        IStorage $storage,
        FileFacadeSelector $fileFacadeSelector

    ) {
        $this->cache = new Cache($storage, static::class);
        $this->selector = $fileFacadeSelector;
    }

    public function getByPersonId($personId)
    {
        throw new NotImplementedException();
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