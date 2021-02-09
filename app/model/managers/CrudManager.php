<?php
/**
 *
 * Created by PhpStorm.
 * Filename: CrudManager.php
 * User: Tomáš Babický
 * Date: 23.08.2020
 * Time: 15:29
 */

namespace Rendix2\FamilyTree\App\Managers;

use dibi;
use Dibi\Connection;
use Dibi\Exception;
use Dibi\Fluent;
use Dibi\Result;
use Dibi\Row;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Model\ICrud;

/**
 * Class CrudManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
abstract class CrudManager extends TableManager implements ICrud
{
    /**
     * @var string $primaryKey
     */
    private $primaryKey;

    /**
     * @var Cache $cache
     */
    private $cache;

    /**
     * @param Fluent $query
     *
     */
    abstract public function getBySubQuery(Fluent $query);

    /**
     * @return Fluent
     */
    abstract public function getAll();

    /**
     * CrudManager constructor.
     *
     * @param Connection $dibi
     *
     * @param IStorage $storage
     * @throws Exception
     */
    public function __construct(
        Connection $dibi,
        IStorage $storage
    ) {
        parent::__construct($dibi, $storage);

        $this->cache = new Cache($storage, static::class);

        $tableName = $this->getTableName();

        $table = $this->dibi->getDatabaseInfo()->getTable($this->getTableName());

        if (count($table->getPrimaryKey()->getColumns()) !== 1) {
            $message = sprintf('Primary key of table "%s" is not on only one column.', $tableName);

            throw new Exception($message);
        }

        if ($table->getPrimaryKey()->getColumns()[0]->getNativeType() !== 'INT') {
            $message = sprintf('Primary key of table "%s" is not integer.', $tableName);

            throw new Exception($message);
        }

        $this->primaryKey = $table->getPrimaryKey()->getColumns()[0]->getName();
    }

    /**
     * @return Cache
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * @param string $column
     *
     * @return array
     */
    public function getPairs($column)
    {
        return $this->getAllFluent()
            ->fetchPairs($this->primaryKey, $column);
    }

    /**
     * @param string $column
     *
     * @return array
     */
    public function getPairsCached($column)
    {
        return $this->cache->call([$this, 'getPairs'], $column);
    }

    /**
     * @param array $data
     *
     * @return Result|int
     */
    public function add($data)
    {
        $res = $this->dibi->insert($this->getTableName(), $data)
            ->execute(dibi::IDENTIFIER);

        $this->cache->clean(self::CACHE_DELETE);

        return $res;
    }

    /**
     * @param int $id
     *
     * @return Row|false
     */
    public function getByPrimaryKey($id)
    {
        return $this->getAllFluent()
            ->where('%n = %i', $this->getPrimaryKey(), $id)
            ->fetch();
    }

    /**
     * @param int $id
     *
     * @return Row|false
     */
    public function getByPrimaryKeyCached($id)
    {
        return $this->getCache()->call([$this, 'getByPrimaryKey'], $id);
    }

    /**
     * @param array $ids
     *
     * @return Row[]
     */
    public function getByPrimaryKeys(array $ids)
    {
        return $this->getAllFluent()
            ->where('%n in %in', $this->getPrimaryKey(), $ids)
            ->fetchAll();
    }

    /**
     * @param array $ids
     *
     * @return Row[]
     */
    public function getByPrimaryKeysCached(array $ids)
    {
        return $this->getCache()->call([$this, 'getByPrimaryKeys'], $ids);
    }

    /**
     * @param int $id
     * @param array $data
     *
     * @return Result|int
     */
    public function updateByPrimaryKey($id, $data)
    {
        $this->cache->clean(self::CACHE_DELETE);

        $res = $this->dibi->update($this->getTableName(), $data)
            ->where('%n = %i', $this->getPrimaryKey(), $id)
            ->execute(dibi::AFFECTED_ROWS);

        return $res;
    }

    /**
     * @param int $id
     *
     * @return Result|int
     */
    public function deleteByPrimaryKey($id)
    {
        $res = $this->dibi->delete($this->getTableName())
            ->where('%n = %i', $this->getPrimaryKey(), $id)
            ->execute(dibi::AFFECTED_ROWS);

        $this->cache->clean(self::CACHE_DELETE);

        return $res;
    }

    /**
     * @param array $ids
     *
     * @return Row[]
     */
    public function checkValues(array &$ids)
    {
        $ids = array_unique($ids);
        sort($ids);
        $count = count($ids);

        if ($count === 0) {
            return [];
        } elseif ($count === 1) {
            if ($ids[0] === null) {
                return [];
            } else {
                $row = $this->getByPrimaryKey($ids[0]);

                return [$row];
            }
        }
    }
}
