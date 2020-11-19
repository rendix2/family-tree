<?php
/**
 *
 * Created by PhpStorm.
 * Filename: s.php
 * User: Tomáš Babický
 * Date: 23.08.2020
 * Time: 15:11
 */

namespace Rendix2\FamilyTree\App\Managers;

use Dibi\Connection;
use Dibi\Exception;
use Dibi\Fluent;
use Dibi\Row;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;

/**
 * Class DibiManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class DibiManager
{
    /**
     * @var string
     */
    const MANAGER = 'Manager';

    /**
     * @var string
     */
    const CACHE_KEY_TABLE = 'table';

    /**
     * @var string
     */
    const CACHE_KEY_ALL = 'all';

    /**
     * @var string
     */
    const CACHE_KEY_PRIMARY = 'primary';

    /**
     * @var string
     */
    const CACHE_KEY_PAIRS = 'pairs';

    /**
     * @var array
     */
    const CACHE_EXPIRE = [Cache::EXPIRE => '20 minutes'];

    /**
     * @var array
     */
    const CACHE_DELETE = [Cache::ALL => true];

    /**
     * @var Connection $dibi
     */
    protected $dibi;

    /**
     * @var string $tableName
     */
    private $tableName;

    /**
     * @var string
     */
    private $tableAlias;

    /**
     * @var Cache $cache
     */
    private $cache;

    /**
     * DibiManager constructor.
     *
     * @param Connection $dibi
     * @param IStorage $storage
     *
     * @throws Exception
     */
    public function __construct(Connection $dibi, IStorage $storage)
    {
        $this->dibi = $dibi;

        $fullClassName = get_class($this);

        $explodedClassName = explode('\\', $fullClassName);
        $explodedCount = count($explodedClassName);

        $className = $explodedClassName[$explodedCount - 1];

        $tableName = str_replace(self::MANAGER, '', $className);
        $tableName = mb_strtolower($tableName);

        if (!$this->dibi->getDatabaseInfo()->hasTable($tableName)) {
            $message = sprintf('Unknown table "%s".', $tableName);

            throw new Exception($message);
        }

        $this->tableName = $tableName;
        $this->tableAlias = mb_substr($tableName, 0, 1);
        $this->cache = new Cache($storage, static::class);
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @return string
     */
    public function getTableAlias()
    {
        return $this->tableAlias;
    }

    /**
     * @return Connection
     */
    public function getDibi()
    {
        return $this->dibi;
    }

    /**
     * @return Fluent
     */
    public function getAllFluent()
    {
        return $this->dibi
            ->select('*')
            ->from($this->tableName);
    }

    /**
     * @return Row[]
     */
    public function getAll()
    {
        return $this->getAllFluent()->fetchAll();
    }

    /**
     * @return Row[]
     */
    public function getAllCached()
    {
       return $this->cache->call([$this, 'getAll']);
    }

    /**
     * @return Fluent
     */
    public function deleteFluent()
    {
        $this->cache->clean(self::CACHE_DELETE);

        return $this->dibi->delete($this->getTableName());
    }
}
