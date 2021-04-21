<?php
/**
 *
 * Created by PhpStorm.
 * Filename: M2NManager.php
 * User: Tomáš Babický
 * Date: 05.04.2021
 * Time: 21:22
 */

namespace Rendix2\FamilyTree\App\Model\Managers\M2NManger;

use Rendix2\FamilyTree\App\Model\CrudManager\DefaultContainer;
use Rendix2\FamilyTree\App\Model\Interfaces\ICrud;
use Rendix2\FamilyTree\App\Model\Interfaces\ITable;
use Rendix2\FamilyTree\App\Model\Managers\M2NManger\Interfaces\IM2NTable;

/**
 * Class M2NManager
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\M2NManger
 */
class M2NManager implements ICrud
{
    /**
     * @var DefaultContainer $defaultContainer
     */
    private $defaultContainer;

    /**
     * @var ITable $leftTable
     */
    private $leftTable;

    /**
     * @var ITable $rightTable
     */
    private $rightTable;

    /**
     * @var IM2NTable $table
     */
    private $table;

    /**
     * M2NManager constructor.
     *
     * @param DefaultContainer $defaultContainer
     * @param IM2NTable        $table
     * @param ITable           $leftTable
     * @param ITable           $rightTable
     */
    public function __construct(
        DefaultContainer $defaultContainer,
        IM2NTable $table,
        ITable $leftTable,
        ITable $rightTable
    ) {
        $this->defaultContainer = $defaultContainer;
        $this->table = $table;

        $this->leftTable = $leftTable;
        $this->rightTable = $rightTable;
    }

    public function __destruct()
    {
        $this->leftTable = null;
        $this->rightTable = null;
        $this->table = null;

        $this->defaultContainer = null;
    }

    /**
     * @return M2NSelectRepository
     */
    public function select()
    {
        return new M2NSelectRepository(
            $this->defaultContainer,
            $this->table,
            $this->leftTable,
            $this->rightTable
        );
    }

    /**
     * @return M2MultiQueryInserter
     */
    public function insert()
    {
        return new M2MultiQueryInserter(
            $this->defaultContainer->getConnection(),
            $this->defaultContainer->getStorage(),
            $this->table
        );
    }

    /**
     * @return M2NUpdater
     */
    public function update()
    {
        return new M2NUpdater(
            $this->defaultContainer->getConnection(),
            $this->defaultContainer->getStorage(),
            $this->table
        );
    }

    /**
     * @return M2NDeleter
     */
    public function delete()
    {
        return new M2NDeleter(
            $this->defaultContainer->getConnection(),
            $this->defaultContainer->getStorage(),
            $this->table
        );
    }
}
