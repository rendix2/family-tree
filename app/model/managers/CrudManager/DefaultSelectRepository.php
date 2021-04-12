<?php
/**
 *
 * Created by PhpStorm.
 * Filename: DefaultSelectRepository.php
 * User: Tomáš Babický
 * Date: 03.04.2021
 * Time: 1:08
 */

namespace Rendix2\FamilyTree\App\Model\CrudManager;

use Dibi\Connection;
use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Filters\IFilter;
use Rendix2\FamilyTree\App\Model\Interfaces\ISelectRepository;
use Rendix2\FamilyTree\App\Model\Interfaces\ITable;

/**
 * Class DefaultSelectRepository
 *
 * @package Rendix2\FamilyTree\App\Model
 */
class DefaultSelectRepository implements ISelectRepository
{
    /**
     * @var Connection $connection
     */
    private $connection;
    /**
     * @var IFilter $filter
     */
    private $filter;

    /**
     * @var IStorage $storage
     */
    private $storage;

    /**
     * @var ITable $table
     */
    private $table;

    /**
     * DefaultSelectRepository constructor.
     *
     * @param Connection $connection
     * @param IStorage   $storage
     * @param ITable     $table
     * @param IFilter    $filter
     */
    public function __construct(
        Connection $connection,
        IStorage $storage,
        ITable $table,
        IFilter $filter
    ) {
        $this->connection = $connection;
        $this->storage = $storage;
        $this->table = $table;
        $this->filter = $filter;
    }

    public function getManager()
    {
        return new DefaultSelector(
            $this->connection,
            $this->table,
            $this->filter
        );
    }

    public function getCachedManager()
    {
        return new DefaultCachedSelector(
            $this->storage,
            $this->getManager()
        );
    }
}
