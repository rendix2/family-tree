<?php
/**
 *
 * Created by PhpStorm.
 * Filename: DefaultContainer.php
 * User: Tomáš Babický
 * Date: 03.04.2021
 * Time: 4:15
 */

namespace Rendix2\FamilyTree\App\Model\CrudManager;


use Dibi\Connection;
use Nette\Caching\IStorage;

/**
 * Class DefaultContainer
 *
 * @package Rendix2\FamilyTree\App\Model\CrudManager
 */
class DefaultContainer
{
    /**
     * @var Connection $connection
     */
    private $connection;

    /**
     * @var IStorage $storage
     */
    private $storage;

    /**
     * DefaultContainer constructor.
     *
     * @param Connection $connection
     * @param IStorage   $storage
     */
    public function __construct(
        Connection $connection,
        IStorage $storage
    ) {
        $this->connection = $connection;
        $this->storage = $storage;
    }

    /**
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return IStorage
     */
    public function getStorage()
    {
        return $this->storage;
    }
}
