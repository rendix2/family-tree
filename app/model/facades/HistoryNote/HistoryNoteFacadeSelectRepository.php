<?php
/**
 *
 * Created by PhpStorm.
 * Filename: HistoryNoteFacadeSelectRepository.php
 * User: Tomáš Babický
 * Date: 08.04.2021
 * Time: 2:20
 */

namespace Rendix2\FamilyTree\App\Model\Facades\HistoryNote;

use Rendix2\FamilyTree\App\Model\Interfaces\ISelectRepository;

/**
 * Class HistoryNoteFacadeSelectRepository
 *
 * @package Rendix2\FamilyTree\App\Model\Facades\HistoryNote
 */
class HistoryNoteFacadeSelectRepository implements ISelectRepository
{

    /**
     * @var HistoryNoteFacadeCachedSelector
     */
    private $historyNoteFacadeCachedSelector;

    /**
     * @var HistoryNoteFacadeSelector
     */
    private $historyNoteFacadeSelector;

    /**
     * HistoryNoteFacadeSelectRepository constructor.
     *
     * @param HistoryNoteFacadeCachedSelector $historyNoteFacadeCachedSelector
     * @param HistoryNoteFacadeSelector       $historyNoteFacadeSelector
     */
    public function __construct(
        HistoryNoteFacadeCachedSelector $historyNoteFacadeCachedSelector,
        HistoryNoteFacadeSelector $historyNoteFacadeSelector
    ) {
        $this->historyNoteFacadeCachedSelector = $historyNoteFacadeCachedSelector;
        $this->historyNoteFacadeSelector = $historyNoteFacadeSelector;
    }

    /**
     * @return HistoryNoteFacadeSelector
     */
    public function getManager()
    {
        return $this->historyNoteFacadeSelector;
    }

    /**
     * @return HistoryNoteFacadeCachedSelector
     */
    public function getCachedManager()
    {
        return $this->historyNoteFacadeCachedSelector;
    }
}