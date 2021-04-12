<?php
/**
 *
 * Created by PhpStorm.
 * Filename: M2NSelectCachedRepository.php
 * User: Tomáš Babický
 * Date: 06.04.2021
 * Time: 0:24
 */

namespace Rendix2\FamilyTree\App\Model\Managers\M2NManger;

use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Model\Managers\M2NManger\Interfaces\IM2NSelector;

/**
 * Class M2NSelectCachedRepository
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\M2NManger
 */
class M2NSelectCachedRepository implements IM2NSelector
{
    /**
     * @var Cache $cache
     */
    private $cache;

    /**
     * @var M2NSelector $selector
     */
    private $selector;

    /**
     * M2NSelectCachedRepository constructor.
     *
     * @param IStorage    $storage
     * @param M2NSelector $selector
     */
    public function __construct(
        IStorage $storage,
        M2NSelector $selector
    ) {
        $this->cache = new Cache($storage, static::class);
        $this->selector = $selector;
    }

    public function getColumnFluent($column)
    {
        return $this->cache->call([$this->selector, 'getColumnFluent'], $column);
    }

    public function getAll()
    {
        return $this->cache->call([$this->selector, 'getAll']);
    }

    public function getByLeftKey($leftId)
    {
        return $this->cache->call([$this->selector, 'getByLeftKey'], $leftId);
    }

    public function getPairsByLeft($leftId)
    {
        return $this->cache->call([$this->selector, 'getPairsByLeft'], $leftId);
    }

    public function getByLeftKeyJoined($leftId)
    {
        return $this->cache->call([$this->selector, 'getByLeftKeyJoined'], $leftId);
    }

    public function getByRightKey($rightId)
    {
        return $this->cache->call([$this->selector, 'getByRightKey'], $rightId);
    }

    public function getPairsByRight($rightId)
    {
        return $this->cache->call([$this->selector, 'getPairsByRight'], $rightId);
    }

    public function getByRightKeyJoined($rightId)
    {
        return $this->cache->call([$this->selector, 'getByRightKeyJoined'], $rightId);
    }

    public function getByLeftAndRightKey($leftId, $rightId)
    {
        return $this->cache->call([$this->selector, 'getByLeftAndRightKey'], $leftId, $rightId);
    }
}
