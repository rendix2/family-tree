<?php
/**
 *
 * Created by PhpStorm.
 * Filename: M2NSingleQueryInsertor.php
 * User: Tomáš Babický
 * Date: 05.04.2021
 * Time: 22:12
 */

namespace Rendix2\FamilyTree\App\Model\Managers\M2NManger;

use Dibi\Connection;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Model\CrudManager\CrudManager;
use Rendix2\FamilyTree\App\Model\Managers\M2NManger\Interfaces\IM2NInserter;
use Rendix2\FamilyTree\App\Model\Managers\M2NManger\Interfaces\IM2NTable;

class M2NSingleQueryInserter implements IM2NInserter
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
     * M2NSingleQueryInserter constructor.
     *
     * @param Connection $connection
     * @param IStorage   $storage
     * @param IM2NTable  $table
     */
    public function __construct(
        Connection  $connection,
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


    public function insert(array $data)
    {
        $this->deleteAllCache();

        return $this->connection->insert(
            $this->table->getTableName(),
            $data
        )->execute();
    }

    public function insertLeftAndRight($leftId, $rightId)
    {
        $this->deleteAllCache();

        // TODO: Implement insertLeftAndRight() method.
    }

    public function insertByLeft($leftId, array $rightIds)
    {
        $this->deleteAllCache();

        // TODO: Implement insertByLeft() method.
    }

    public function insertByRight(array $leftIds, $rightId)
    {
        $this->deleteAllCache();

        // TODO: Implement insertByRight() method.
    }
}
