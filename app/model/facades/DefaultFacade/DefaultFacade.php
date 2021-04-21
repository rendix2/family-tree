<?php
/**
 *
 * Created by PhpStorm.
 * Filename: DefaultFacade.php
 * User: Tomáš Babický
 * Date: 05.04.2021
 * Time: 17:31
 */

namespace Rendix2\FamilyTree\App\Model\Facades\DefaultFacade;

use Rendix2\FamilyTree\App\Model\CrudManager\DefaultContainer;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultDeleter;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultInserter;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultUpdater;
use Rendix2\FamilyTree\App\Model\Interfaces\ICrud;
use Rendix2\FamilyTree\App\Model\Interfaces\ITable;

/**
 * Class DefaultFacade
 *
 * @package Rendix2\FamilyTree\App\Model\Facades\DefaultFacade
 */
abstract class DefaultFacade implements ICrud
{
    /**
     * @var ICrud $crudManager
     */
    private $crudManager;

    /**
     * @var DefaultContainer $defaultContainer
     */
    private $defaultContainer;

    /**
     * @var ITable $table
     */
    private $table;

    /**
     * DefaultFacade constructor.
     *
     * @param DefaultContainer $defaultContainer
     * @param ITable           $table
     * @param ICrud            $crudManager
     */
    public function __construct(
        DefaultContainer $defaultContainer,
        ITable $table,
        ICrud $crudManager
    ) {
        $this->crudManager = $crudManager;
        $this->defaultContainer = $defaultContainer;
        $this->table = $table;
    }

    public function __destruct()
    {
        $this->table = null;
        $this->crudManager = null;
        $this->defaultContainer = null;
    }

    abstract public function select();

    /**
     * @return DefaultInserter
     */
    public function insert()
    {
        return new DefaultInserter(
            $this->defaultContainer->getConnection(),
            $this->defaultContainer->getStorage(),
            $this->table
        );
    }

    /**
     * @return DefaultUpdater
     */
    public function update()
    {
        return new DefaultUpdater(
            $this->defaultContainer->getConnection(),
            $this->defaultContainer->getStorage(),
            $this->table
        );
    }

    /**
     * @return DefaultDeleter
     */
    public function delete()
    {
        return new DefaultDeleter(
            $this->defaultContainer->getConnection(),
            $this->defaultContainer->getStorage(),
            $this->table
        );
    }

    /**
     * @return ICrud
     */
    public function getManager()
    {
        return $this->crudManager;
    }
}
