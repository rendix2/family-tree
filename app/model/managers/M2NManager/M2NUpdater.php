<?php
/**
 *
 * Created by PhpStorm.
 * Filename: M2NUpdator.php
 * User: Tomáš Babický
 * Date: 05.04.2021
 * Time: 23:58
 */

namespace Rendix2\FamilyTree\App\Model\Managers\M2NManger;

use Dibi\Connection;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Model\CrudManager\CrudManager;
use Rendix2\FamilyTree\App\Model\Managers\M2NManger\Interfaces\IM2NTable;
use Rendix2\FamilyTree\App\Model\Managers\M2NManger\Interfaces\IM2NUpdater;

/**
 * Class M2NUpdater
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\M2NManger
 */
class M2NUpdater implements IM2NUpdater
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
     * M2NUpdater constructor.
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

    public function updateByLeftAndRight($leftId, $rightId, array $data)
    {
        $this->deleteAllCache();

        return $this->connection->update(
            $this->table->getTableName(),
            $data
        )
            ->where('%n = %i', $this->table->getLeftPrimaryKey(), $leftId)
            ->where('%n = %i', $this->table->getRightPrimaryKey(), $rightId)
            ->execute();
    }
}
