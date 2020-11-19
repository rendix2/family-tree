<?php
/**
 *
 * Created by PhpStorm.
 * Filename: HistoryNoteFacade.php
 * User: Tomáš Babický
 * Date: 16.11.2020
 * Time: 1:13
 */

namespace Rendix2\FamilyTree\App\Model\Facades;

use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Managers\NoteHistoryManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Model\Entities\HistoryNoteEntity;
use Rendix2\FamilyTree\App\Model\Entities\PersonEntity;

class HistoryNoteFacade
{
    /**
     * @var NoteHistoryManager $historyNoteManager
     */
    private $historyNoteManager;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * HistoryNoteFacade constructor.
     *
     * @param NoteHistoryManager $historyNoteManager
     * @param PersonManager $personManager
     */
    public function __construct(
        IStorage $storage,
        NoteHistoryManager $historyNoteManager,
        PersonManager $personManager
    ) {
        $this->cache = new Cache($storage, self::class);
        $this->historyNoteManager = $historyNoteManager;
        $this->personManager = $personManager;
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

    /**
     * @return HistoryNoteEntity[]
     */
    public function getAll()
    {
        $historyNotes = $this->historyNoteManager->getAll();
        $persons = $this->personManager->getAll();

        return $this->join($historyNotes, $persons);
    }

    /**
     * @return HistoryNoteEntity[]
     */
    public function getAllCached()
    {
        return $this->cache->call([$this, 'getAll']);
    }

    /**
     * @param int $historyNoteId
     *
     * @return HistoryNoteEntity
     */
    public function getByPrimaryKey($historyNoteId)
    {
        $historyNote = $this->historyNoteManager->getByPrimaryKey($historyNoteId);
        $persons = $this->personManager->getAll();

        return $this->join([$historyNote], $persons)[0];
    }

    /**
     * @param int $historyNoteId
     *
     * @return HistoryNoteEntity[]
     */
    public function getByPrimaryKeyCached($historyNoteId)
    {
        return $this->cache->call([$this, 'getByPrimaryKey'], $historyNoteId);
    }

    /**
     * @param int $personId
     *
     * @return HistoryNoteEntity[]
     */
    public function getByPerson($personId)
    {
        $historyNotes = $this->historyNoteManager->getByPerson($personId);
        $person = $this->personManager->getByPrimaryKey($personId);

        return $this->join($historyNotes, [$person]);
    }

    /**
     * @param int $personId
     *
     * @return HistoryNoteEntity[]
     */
    public function getByPersonCached($personId)
    {
        return $this->cache->call([$this, 'getByPerson'], $personId);
    }
}
