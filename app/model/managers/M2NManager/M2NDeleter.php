<?php
/**
 *
 * Created by PhpStorm.
 * Filename: M2NDeletor.php
 * User: Tomáš Babický
 * Date: 05.04.2021
 * Time: 22:30
 */

namespace Rendix2\FamilyTree\App\Model\Managers\M2NManger;

use Dibi\Connection;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Model\CrudManager\CrudManager;
use Rendix2\FamilyTree\App\Model\Managers\M2NManger\Interfaces\IM2NTable;

/**
 * Class M2NDeleter
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\M2NManger
 */
class M2NDeleter implements Interfaces\IM2NDeleter
{
    /**
     * @var Cache $cache
     */
    private $cache;

    /**
     * @var Connection $connection
     */
    private $connection;

    /**
     * @var IM2NTable $table
     */
    private $table;

    /**
     * M2NDeleter constructor.
     *
     * @param Connection $connection
     * @param IStorage   $storage
     * @param IM2NTable  $table
     */
    public function __construct(
        Connection $connection,
        IStorage $storage,
        IM2NTable $table
    ) {
        $this->cache = new Cache($storage, static::class);
        $this->connection = $connection;
        $this->table = $table;
    }

    public function deleteAllCache()
    {
        $this->cache->clean(CrudManager::CACHE_DELETE);
    }

    public function deleteByLeftKey($leftId)
    {
        $this->deleteAllCache();

        return $this->connection
            ->delete($this->table->getTableName())
            ->where('%n = %i', $this->table->getLeftPrimaryKey(), $leftId)
            ->execute();
    }

    public function deleteByRightKey($rightId)
    {
        $this->deleteAllCache();

        return $this->connection
            ->delete($this->table->getTableName())
            ->where('%n = %i', $this->table->getRightPrimaryKey(), $rightId)
            ->execute();
    }

    public function deleteByLeftAndRightKey($leftId, $rightId)
    {
        $this->deleteAllCache();

        return $this->connection
            ->delete($this->table->getTableName())
            ->where('%n = %i', $this->table->getLeftPrimaryKey(), $leftId)
            ->where('%n = %i', $this->table->getRightPrimaryKey(), $rightId)
            ->execute();
    }
}
