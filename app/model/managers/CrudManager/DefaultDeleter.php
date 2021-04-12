<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PrimaryKeyDeleter.php
 * User: Tomáš Babický
 * Date: 02.04.2021
 * Time: 21:20
 */

namespace Rendix2\FamilyTree\App\Model\CrudManager;

use Dibi\Connection;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Model\Interfaces\IDeleter;
use Rendix2\FamilyTree\App\Model\Interfaces\ITable;

/**
 * Class PrimaryKeyDeleter
 *
 * @package Rendix2\FamilyTree\App\Model
 */
class DefaultDeleter implements IDeleter
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
     * PrimaryKeyDeleter constructor.
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

    /**
     * @return Cache
     */
    public function getCache()
    {
        return $this->cache;
    }


    /**
     * @return Connection
     */
    protected function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return ITable
     */
    protected function getTable()
    {
        return $this->table;
    }

    public function deleteAllCache()
    {
        $this->cache->clean(CrudManager::CACHE_DELETE);
    }

    protected function deleteFluent()
    {
        return $this->connection->delete($this->table->getTableName());
    }

    public function deleteByPrimaryKey($id)
    {
        $this->deleteAllCache();

         return $this->connection->delete($this->table->getTableName())
             ->where('%n = %i', $this->table->getPrimaryKey(), $id)
             ->execute();
    }
}
