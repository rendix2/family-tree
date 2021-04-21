<?php
/**
 *
 * Created by PhpStorm.
 * Filename: Person2JobFacadeCachedSelector.php
 * User: Tomáš Babický
 * Date: 12.04.2021
 * Time: 0:58
 */

namespace Rendix2\FamilyTree\App\Model\Facades\Person2Job;

use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\NotImplementedException;
use Rendix2\FamilyTree\App\Model\Managers\M2NManger\Interfaces\IM2NSelector;

/**
 * Class Person2JobFacadeCachedSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Facades\Person2Job
 */
class Person2JobFacadeCachedSelector implements IM2NSelector
{
    /**
     * @var Cache $cache
     */
    private $cache;

    /**
     * @var Person2JobFacadeSelector $selector
     */
    private $selector;

    /**
     * Person2JobFacadeCachedSelector constructor.
     *
     * @param IStorage                 $storage
     * @param Person2JobFacadeSelector $selector
     */
    public function __construct(
        IStorage $storage,
        Person2JobFacadeSelector $selector
    ) {
        $this->cache = new Cache($storage, static::class);
        $this->selector = $selector;
    }

    public function __destruct()
    {
        $this->cache = null;
        $this->selector = null;
    }

    public function getAll()
    {
        return $this->cache->call([$this->selector, 'getAll']);
    }

    public function getColumnFluent($column)
    {
        throw new NotImplementedException();
    }

    public function getByLeftKey($leftId)
    {
        return $this->cache->call([$this->selector, 'getByLeftKey'], $leftId);
    }

    public function getPairsByLeft($leftId)
    {
        throw new NotImplementedException();
    }

    public function getByLeftKeyJoined($leftId)
    {
        throw new NotImplementedException();
    }

    public function getByRightKey($rightId)
    {
        return $this->cache->call([$this->selector, 'getByRightKey'], $rightId);
    }

    public function getPairsByRight($rightId)
    {
        throw new NotImplementedException();
    }

    public function getByRightKeyJoined($rightId)
    {
        throw new NotImplementedException();
    }

    public function getByLeftAndRightKey($leftId, $rightId)
    {
        return $this->cache->call([$this->selector, 'getByLeftAndRightKey'], $leftId, $rightId);
    }
}
