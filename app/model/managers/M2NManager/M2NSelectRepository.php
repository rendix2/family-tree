<?php
/**
 *
 * Created by PhpStorm.
 * Filename: M2NSelectRepository.php
 * User: Tomáš Babický
 * Date: 05.04.2021
 * Time: 21:24
 */

namespace Rendix2\FamilyTree\App\Model\Managers\M2NManger;

use Rendix2\FamilyTree\App\Model\CrudManager\DefaultContainer;
use Rendix2\FamilyTree\App\Model\Interfaces\ISelectRepository;
use Rendix2\FamilyTree\App\Model\Interfaces\ITable;
use Rendix2\FamilyTree\App\Model\Managers\M2NManger\Interfaces\IM2NTable;

class M2NSelectRepository implements ISelectRepository
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
     * M2NSelectRepository constructor.
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

    /**
     * @return M2NSelector
     */
    public function getManager()
    {
        return new M2NSelector(
            $this->defaultContainer->getConnection(),
            $this->table,
            $this->leftTable,
            $this->rightTable
        );
    }

    /**
     * @return M2NSelectCachedRepository
     */
    public function getCachedManager()
    {
        return new M2NSelectCachedRepository(
            $this->defaultContainer->getStorage(),
            $this->getManager()
        );
    }
}
