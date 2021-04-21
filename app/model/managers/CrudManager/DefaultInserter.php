<?php
/**
 *
 * Created by PhpStorm.
 * Filename: Inserter.php
 * User: Tomáš Babický
 * Date: 02.04.2021
 * Time: 21:08
 */

namespace Rendix2\FamilyTree\App\Model\CrudManager;

use dibi;
use Dibi\Connection;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Model\Interfaces\IInserter;
use Rendix2\FamilyTree\App\Model\Interfaces\ITable;

/**
 * Class DefaultInserter
 *
 * @package Rendix2\FamilyTree\App\Model
 */
class DefaultInserter implements IInserter
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
     * DefaultInserter constructor.
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
        $this->table = null;
    }

    public function deleteAllCache()
    {
        $this->cache->clean(CrudManager::CACHE_DELETE);
    }

    public function insert(array $data)
    {
        $this->deleteAllCache();

        return $this->connection
            ->insert($this->table->getTableName(), $data)
            ->execute(dibi::IDENTIFIER);
    }
}
