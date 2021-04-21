<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PrimaryKeyUpdater.php
 * User: Tomáš Babický
 * Date: 02.04.2021
 * Time: 21:17
 */

namespace Rendix2\FamilyTree\App\Model\CrudManager;

use Dibi\Connection;
use Dibi\Result;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Model\Interfaces\ITable;
use Rendix2\FamilyTree\App\Model\Interfaces\IUpdater;

/**
 * Class PrimaryKeyUpdater
 *
 * @package Rendix2\FamilyTree\App\Model
 */
class DefaultUpdater implements IUpdater
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
     * @var ITable $table
     */
    private $table;

    /**
     * PrimaryKeyUpdater constructor.
     *
     * @param Connection $connection
     * @param IStorage   $storage
     * @param ITable     $table
     */
    public function __construct(
        Connection $connection,
        IStorage $storage,
        ITable $table
    ) {
        $this->cache = new Cache($storage, static::class);
        $this->connection = $connection;
        $this->table = $table;
    }

    public function __destruct()
    {
        $this->cache = null;
        $this->connection = null;
        $this->cache = null;
    }

    public function deleteAllCache()
    {
        $this->cache->clean(CrudManager::CACHE_DELETE);
    }

    /**
     * @param int $id
     * @param array $data
     *
     * @return Result|int
     */
    public function updateByPrimaryKey($id, array $data)
    {
        $this->deleteAllCache();

        return $this->connection->update($this->table->getTableName(), $data)
            ->where('%n = %i', $this->table->getPrimaryKey(), $id)
            ->execute();
    }
}
