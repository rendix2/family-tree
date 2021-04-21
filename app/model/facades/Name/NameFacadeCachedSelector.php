<?php
/**
 *
 * Created by PhpStorm.
 * Filename: NameFacadeCachedSelector.php
 * User: Tomáš Babický
 * Date: 10.04.2021
 * Time: 2:14
 */

namespace Rendix2\FamilyTree\App\Model\Facades\Name;


use Dibi\Fluent;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\NotImplementedException;
use Rendix2\FamilyTree\App\Model\Entities\NameEntity;
use Rendix2\FamilyTree\App\Model\Managers\Name\Interfaces\INameSelector;

class NameFacadeCachedSelector implements INameSelector
{
    /**
     * @var Cache $cache
     */
    private $cache;

    /**
     * @var NameFacadeSelector $selector
     */
    private $selector;

    /**
     * NameFacadeCachedSelector constructor.
     *
     * @param IStorage           $storage
     * @param NameFacadeSelector $nameFacadeSelector
     */
    public function __construct(
        IStorage $storage,
        NameFacadeSelector $nameFacadeSelector
    ) {
        $this->cache = new Cache($storage, self::class);
        $this->selector = $nameFacadeSelector;
    }

    public function __destruct()
    {
        $this->cache = null;
        $this->selector = null;
    }

    public function getByPersonId($personId)
    {
        return $this->cache->call([$this->selector, 'getByPersonId'], $personId);
    }

    /**
     * @param int $genusId
     *
     * @return NameEntity[]
     */
    public function getByGenusId($genusId)
    {
        return $this->cache->call([$this->selector, 'getByGenusId'], $genusId);
    }

    /**
     * @param int $id
     *
     * @return NameEntity
     */
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

    /**
     * @return NameEntity[]
     */
    public function getAllCached()
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

    public function getAll()
    {
        return $this->cache->call([$this->selector, 'getAll']);
    }
}
