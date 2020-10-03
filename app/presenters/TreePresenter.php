<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TreePresenter.php
 * User: Tomáš Babický
 * Date: 23.08.2020
 * Time: 22:42
 */

namespace Rendix2\FamilyTree\App\Presenters;

use Rendix2\FamilyTree\App\Managers\TreeManager;

/**
 * Class TreePresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class TreePresenter extends BasePresenter
{
    /**
     * @var TreeManager $treeManager
     */
    private $treeManager;

    /**
     * TreePresenter constructor.
     * @param TreeManager $treeManager
     */
    public function __construct(TreeManager $treeManager)
    {
        parent::__construct();

        $this->treeManager = $treeManager;
    }

    /**
     * @return void
     */
    public function handleAllTree()
    {
        $this->sendJson($this->treeManager->getAllFamilyTree());
    }

    /**
     * @param int $genusId
     */
    public function handleGenusTree($genusId)
    {
        $this->sendJson($this->treeManager->getGenusTree($genusId));
    }

    /**
     * @param int $personId
     */
    public function handlePersonTree($personId)
    {
        $this->sendJson($this->treeManager->getPersonTree($personId));
    }
}
