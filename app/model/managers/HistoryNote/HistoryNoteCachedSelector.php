<?php
/**
 *
 * Created by PhpStorm.
 * Filename: HistoryNoteCachedSelector.php
 * User: Tomáš Babický
 * Date: 05.04.2021
 * Time: 0:47
 */

namespace Rendix2\FamilyTree\App\Model\Managers\HistoryNote;

use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultCachedSelector;
use Rendix2\FamilyTree\App\Model\Managers\HistoryNote\Interfaces\IHistoryNoteSelector;

/**
 * Class HistoryNoteCachedSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\HistoryNote
 */
class HistoryNoteCachedSelector extends DefaultCachedSelector implements IHistoryNoteSelector
{
    /**
     * HistoryNoteCachedSelector constructor.
     *
     * @param IStorage            $storage
     * @param HistoryNoteSelector $selector
     */
    public function __construct(
        IStorage $storage,
        HistoryNoteSelector $selector
    ) {
        parent::__construct($storage, $selector);
    }

    public function getByPersonId($personId)
    {
        return $this->getCache()->call([$this->getSelector(), 'getByPersonId'], $personId);
    }
}
