<?php
/**
 *
 * Created by PhpStorm.
 * Filename: CrudManager.php
 * User: Tomáš Babický
 * Date: 03.04.2021
 * Time: 1:35
 */

namespace Rendix2\FamilyTree\App\Model\CrudManager;

use Nette\Caching\Cache;
use Rendix2\FamilyTree\App\Filters\IFilter;
use Rendix2\FamilyTree\App\Model\Interfaces\ICrud;
use Rendix2\FamilyTree\App\Model\Interfaces\ITable;

/**
 * Class CrudManager
 *
 * @package Rendix2\FamilyTree\App\Model
 */
abstract class CrudManager implements ICrud
{
    /**
     * @var array
     */
    const CACHE_DELETE = [Cache::ALL => true];

    /**
     * @var DefaultContainer $defaultContainer
     */
    private $defaultContainer;

    /**
     * @var IFilter $filter
     */
    private $filter;

    /**
     * @var ITable $table
     */
    protected $table;

    /**
     * CrudManager constructor.
     *
     * @param DefaultContainer $defaultContainer
     * @param ITable           $table
     * @param IFilter          $filter
     */
    public function __construct(
        DefaultContainer $defaultContainer,
        ITable $table,
        IFilter $filter
    ) {
        $this->defaultContainer = $defaultContainer;
        $this->table = $table;
        $this->filter = $filter;
    }

    /**
     * @return ITable
     */
    public function getTable()
    {
        return $this->table;
    }

    public function select()
    {
        return new DefaultSelectRepository(
            $this->defaultContainer->getConnection(),
            $this->defaultContainer->getStorage(),
            $this->table,
            $this->filter
        );
    }

    public function insert()
    {
        return new DefaultInserter(
            $this->defaultContainer->getConnection(),
            $this->defaultContainer->getStorage(),
            $this->table
        );
    }

    public function update()
    {
        return new DefaultUpdater(
            $this->defaultContainer->getConnection(),
            $this->defaultContainer->getStorage(),
            $this->table
        );
    }

    public function delete()
    {
        return new DefaultDeleter(
            $this->defaultContainer->getConnection(),
            $this->defaultContainer->getStorage(),
            $this->table
        );
    }
}
