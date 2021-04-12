<?php
/**
 *
 * Created by PhpStorm.
 * Filename: HistoryNoteFilter.php
 * User: Tomáš Babický
 * Date: 24.11.2020
 * Time: 16:04
 */

namespace Rendix2\FamilyTree\App\Filters;

use Rendix2\FamilyTree\App\Model\Entities\HistoryNoteEntity;

/**
 * Class HistoryNoteFilter
 *
 * @package Rendix2\FamilyTree\App\Filters
 */
class HistoryNoteFilter implements IFilter
{
    /**
     * @param HistoryNoteEntity $historyNoteEntity
     *
     * @return int
     */
    public function __invoke(HistoryNoteEntity $historyNoteEntity)
    {
        return $historyNoteEntity->id;
    }
}
