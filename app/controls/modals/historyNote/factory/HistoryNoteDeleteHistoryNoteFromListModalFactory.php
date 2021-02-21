<?php
/**
 *
 * Created by PhpStorm.
 * Filename: HistoryNoteDeleteHistoryNoteFromListModal.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 2:00
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\HistoryNote\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\HistoryNote\HistoryNoteDeleteHistoryNoteFromListModal;

/**
 * Interface HistoryNoteDeleteHistoryNoteFromListModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\HistoryNote\Factory
 */
interface HistoryNoteDeleteHistoryNoteFromListModalFactory
{
    /**
     * @return HistoryNoteDeleteHistoryNoteFromListModal
     */
    public function create();
}
