<?php
/**
 *
 * Created by PhpStorm.
 * Filename: NameSelectRepository.php
 * User: Tomáš Babický
 * Date: 06.04.2021
 * Time: 22:56
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Name;

use Dibi\Connection;
use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Filters\NameFilter;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultSelectRepository;
use Rendix2\FamilyTree\App\Model\Tables\NameTable;

/**
 * Class NameSelectRepository
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Name
 */
class NameSelectRepository extends DefaultSelectRepository
{
    /**
     * @var NameCachedSelector $nameCachedSelector
     */
    private $nameCachedSelector;

    /**
     * @var NameSelector $nameSelector
     */
    private $nameSelector;

    /***
     * NameSelectRepository constructor.
     *
     * @param Connection         $connection
     * @param IStorage           $storage
     * @param NameTable          $table
     * @param NameFilter         $filter
     * @param NameSelector       $nameSelector
     * @param NameCachedSelector $nameCachedSelector
     */
    public function __construct(
        Connection $connection,
        IStorage $storage,
        NameTable $table,
        NameFilter $filter,

        NameSelector $nameSelector,
        NameCachedSelector $nameCachedSelector
    ) {
        parent::__construct($connection, $storage, $table, $filter);

        $this->nameCachedSelector = $nameCachedSelector;
        $this->nameSelector = $nameSelector;
    }

    /**
     * @return NameSelector
     */
    public function getManager()
    {
        return $this->nameSelector;
    }

    /**
     * @return NameCachedSelector
     */
    public function getCachedManager()
    {
        return $this->nameCachedSelector;
    }
}
