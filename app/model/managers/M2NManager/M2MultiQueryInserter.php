<?php
/**
 *
 * Created by PhpStorm.
 * Filename: M2MultiQueryInserter.php
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

/**
 * Class M2MultiQueryInserter
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\M2NManger
 */
class M2MultiQueryInserter implements IM2NInserter
{
    /**
     * @var Cache $cache
     */
    private $cache;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var IM2NTable $table
     */
    private $table;

    /**
     * M2MultiQueryInserter constructor.
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
        $insertRow = [
            $this->table->getLeftPrimaryKey() => $leftId,
            $this->table->getRightPrimaryKey() => $rightId,
        ];

        $this->deleteAllCache();

        return $this->connection->insert(
            $this->table->getTableName(),
            $insertRow
        )->execute();
    }

    public function insertByLeft($leftId, array $rightIds)
    {
        foreach ($rightIds as $rightId) {
            $this->insertLeftAndRight($leftId, $rightId);
        }
    }

    public function insertByRight(array $leftIds, $rightId)
    {
        foreach ($leftIds as $leftId) {
            $this->insertLeftAndRight($leftId, $rightId);
        }
    }
}