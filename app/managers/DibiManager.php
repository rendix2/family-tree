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

/**
 * Class DibiManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class DibiManager
{
    /**
     *
     */
    const MANAGER = 'Manager';

    /**
     * @var Connection $dibi
     */
    protected $dibi;

    /**
     * @var string $tableName
     */
    private $tableName;

    /**
     * DibiManager constructor.
     *
     * @param Connection $dibi
     * @throws Exception
     */
    public function __construct(Connection $dibi)
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
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
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
     * @return Fluent
     */
    public function deleteFluent()
    {
        return $this->dibi->delete($this->getTableName());
    }
}
