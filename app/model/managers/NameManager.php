<?php
/**
 *
 * Created by PhpStorm.
 * Filename: NameManager.php
 * User: Tomáš Babický
 * Date: 02.04.2021
 * Time: 15:11
 */

namespace Rendix2\FamilyTree\App\Model\Managers;

use Rendix2\FamilyTree\App\Filters\NameFilter;
use Rendix2\FamilyTree\App\Model\CrudManager\CrudManager;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultContainer;
use Rendix2\FamilyTree\App\Model\Managers\Name\NameDeleter;
use Rendix2\FamilyTree\App\Model\Managers\Name\NameSelectRepository;
use Rendix2\FamilyTree\App\Model\Tables\NameTable;

/**
 * Class NameManager
 *
 * @package Rendix2\FamilyTree\App\Model\Managers
 */
class NameManager extends CrudManager
{
    /**
     * @var NameDeleter $nameDeleter
     */
    private $nameDeleter;

    /**
     * @var NameSelectRepository $nameSelectRepository
     */
    private $nameSelectRepository;

    /**
     * NameManager constructor.
     *
     * @param DefaultContainer     $defaultContainer
     * @param NameTable            $table
     * @param NameFilter           $filter
     * @param NameSelectRepository $nameSelectRepository
     * @param NameDeleter          $nameDeleter
     */
    public function __construct(
        DefaultContainer $defaultContainer,
        NameTable $table,
        NameFilter $filter,
        NameSelectRepository $nameSelectRepository,
        NameDeleter $nameDeleter
    ) {
        parent::__construct($defaultContainer, $table, $filter);

        $this->nameSelectRepository = $nameSelectRepository;
        $this->nameDeleter = $nameDeleter;
    }

    /**
     * @return NameSelectRepository
     */
    public function select()
    {
        return $this->nameSelectRepository;
    }

    /**
     * @return NameDeleter
     */
    public function delete()
    {
        return $this->nameDeleter;
    }
}
