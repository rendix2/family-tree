<?php
/**
 *
 * Created by PhpStorm.
 * Filename: HistoryNoteSelectRepository.php
 * User: Tomáš Babický
 * Date: 05.04.2021
 * Time: 0:45
 */

namespace Rendix2\FamilyTree\App\Model\Managers\HistoryNote;


use Dibi\Connection;
use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Filters\HistoryNoteFilter;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultSelectRepository;

class HistoryNoteSelectRepository extends DefaultSelectRepository
{
    /**
     * @var HistoryNoteSelector $historyNoteSelector
     */
    private $historyNoteSelector;

    /**
     * @var HistoryNoteCachedSelector $historyNoteCachedSelector
     */
    private $historyNoteCachedSelector;

    /**
     * HistoryNoteSelectRepository constructor.
     *
     * @param Connection                $connection
     * @param IStorage                  $storage
     * @param HistoryNoteFilter         $historyNoteFilter
     * @param HistoryNoteTable          $table
     * @param HistoryNoteSelector       $historyNoteSelector
     * @param HistoryNoteCachedSelector $historyNoteCachedSelector
     */
    public function __construct(
        Connection $connection,
        IStorage $storage,
        HistoryNoteFilter $historyNoteFilter,
        HistoryNoteTable $table,
        HistoryNoteSelector $historyNoteSelector,
        HistoryNoteCachedSelector $historyNoteCachedSelector
    ) {
        parent::__construct($connection, $storage, $table, $historyNoteFilter);

        $this->historyNoteCachedSelector = $historyNoteCachedSelector;
        $this->historyNoteSelector = $historyNoteSelector;
    }

    /**
     * @return HistoryNoteSelector
     */
    public function getManager()
    {
        return $this->historyNoteSelector;
    }

    /**
     * @return HistoryNoteCachedSelector
     */
    public function getCachedManager()
    {
        return $this->historyNoteCachedSelector;
    }
}
