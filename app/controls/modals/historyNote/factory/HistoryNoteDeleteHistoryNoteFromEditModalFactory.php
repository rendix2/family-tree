<?php
/**
 *
 * Created by PhpStorm.
 * Filename: HistoryNoteDeleteHistoryNoteFromEditModalFactory.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 2:00
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\HistoryNote\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\HistoryNote\HistoryNoteDeleteHistoryNoteFromEditModal;

/**
 * Interface HistoryNoteDeleteHistoryNoteFromEditModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\HistoryNote\Factory
 */
interface HistoryNoteDeleteHistoryNoteFromEditModalFactory
{
    /**
     * @return HistoryNoteDeleteHistoryNoteFromEditModal
     */
    public function create();
}
