<?php
/**
 *
 * Created by PhpStorm.
 * Filename: HistoryNoteSelector.php
 * User: Tomáš Babický
 * Date: 08.04.2021
 * Time: 2:19
 */

namespace Rendix2\FamilyTree\App\Model\Facades\HistoryNote;


use Dibi\Fluent;
use Nette\NotImplementedException;
use Rendix2\FamilyTree\App\Filters\HistoryNoteFilter;
use Rendix2\FamilyTree\App\Model\Entities\HistoryNoteEntity;
use Rendix2\FamilyTree\App\Model\Entities\PersonEntity;
use Rendix2\FamilyTree\App\Model\Facades\DefaultFacade\DefaultFacadeSelector;
use Rendix2\FamilyTree\App\Model\Managers\HistoryNote\Interfaces\IHistoryNoteSelector;
use Rendix2\FamilyTree\App\Model\Managers\HistoryNoteManager;
use Rendix2\FamilyTree\App\Model\Managers\PersonManager;

/**
 * Class HistoryNoteFacadeSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Facades\HistoryNote
 */
class HistoryNoteFacadeSelector extends DefaultFacadeSelector implements IHistoryNoteSelector
{
    /**
     * @var HistoryNoteManager $historyNoteManager
     */
    private $historyNoteManager;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * HistoryNoteFacade constructor.
     *
     * @param HistoryNoteFilter  $historyNoteFilter
     * @param HistoryNoteManager $historyNoteManager
     * @param PersonManager      $personManager
     */
    public function __construct(
        HistoryNoteFilter $historyNoteFilter,
        HistoryNoteManager $historyNoteManager,
        PersonManager $personManager
    ) {
        parent::__construct($historyNoteFilter);

        $this->historyNoteManager = $historyNoteManager;
        $this->personManager = $personManager;
    }

    public function __destruct()
    {
        $this->historyNoteManager = null;
        $this->personManager = null;

        parent::__destruct();
    }

    /**
     * @param HistoryNoteEntity[] $historyNotes
     * @param PersonEntity[] $persons
     *
     * @return HistoryNoteEntity[]
     */
    public function join(array $historyNotes, array $persons)
    {
        foreach ($historyNotes as $historyNote) {
            foreach ($persons as $person) {
                if ($historyNote->_personId === $person->id) {
                    $historyNote->person = $person;
                    break;
                }
            }

            $historyNote->clean();
        }

        return $historyNotes;
    }

    public function getByPersonId($personId)
    {
        $historyNotes = $this->historyNoteManager->select()->getManager()->getByPersonId($personId);
        $person = $this->personManager->select()->getManager()->getByPrimaryKey($personId);

        return $this->join($historyNotes, [$person]);
    }

    public function getByPrimaryKey($id)
    {
        $historyNote = $this->historyNoteManager->select()->getManager()->getByPrimaryKey($id);

        if (!$historyNote) {
            return null;
        }

        $person = $this->personManager->select()->getManager()->getByPrimaryKey($historyNote->_personId);

        return $this->join([$historyNote], [$person])[0];
    }

    public function getByPrimaryKeys(array $ids)
    {
        throw new NotImplementedException();
    }

    public function getColumnFluent($column)
    {
        throw new NotImplementedException();
    }

    public function getAll()
    {
        $historyNotes = $this->historyNoteManager->select()->getCachedManager()->getAll();

        $persons = $this->personManager->select()->getCachedManager()->getAll();

        return $this->join($historyNotes, $persons);
    }

    public function getAllPairs()
    {
        throw new NotImplementedException();
    }

    public function getPairs($column)
    {
        throw new NotImplementedException();
    }

    public function getBySubQuery(Fluent $query)
    {
        throw new NotImplementedException();
    }
}