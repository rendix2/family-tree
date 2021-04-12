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

use Rendix2\FamilyTree\App\Services\TreeService;

/**
 * Class TreePresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class TreePresenter extends BasePresenter
{
    /**
     * @var $treeService $treeService
     */
    private $treeService;

    /**
     * TreePresenter constructor.
     *
     * @param $treeService $treeService
     */
    public function __construct(TreeService $treeService)
    {
        parent::__construct();

        $this->treeService = $treeService;
    }

    /**
     * @return void
     */
    public function handleAllTree()
    {
        $this->sendJson($this->treeService->getAllFamilyTree());
    }

    /**
     * @param int $genusId
     */
    public function handleGenusTree($genusId)
    {
        $this->sendJson($this->treeService->getGenusTree($genusId));
    }

    /**
     * @param int $personId
     */
    public function handlePersonTree($personId)
    {
        $this->sendJson($this->treeService->getPersonTree($personId));
    }
}
