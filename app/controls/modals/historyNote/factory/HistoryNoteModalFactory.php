<?php
/**
 *
 * Created by PhpStorm.
 * Filename: HistoryNoteModalFactory.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 2:34
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\HistoryNote\Factory;

/**
 * Class HistoryNoteModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\HistoryNote\Factory
 */
class HistoryNoteModalFactory
{
    /**
     * @var HistoryNoteDeleteHistoryNoteFromEditModalFactory $historyNoteDeleteHistoryNoteFromEditModalFactory
     */
    private $historyNoteDeleteHistoryNoteFromEditModalFactory;

    /**
     * @var HistoryNoteDeleteHistoryNoteFromListModalFactory $historyNoteDeleteHistoryNoteFromListModalFactory
     */
    private $historyNoteDeleteHistoryNoteFromListModalFactory;

    /**
     * HistoryNoteModalFactory constructor.
     *
     * @param HistoryNoteDeleteHistoryNoteFromEditModalFactory $historyNoteDeleteHistoryNoteFromEditModalFactory
     * @param HistoryNoteDeleteHistoryNoteFromListModalFactory $historyNoteDeleteHistoryNoteFromListModalFactory
     */
    public function __construct(
        HistoryNoteDeleteHistoryNoteFromEditModalFactory $historyNoteDeleteHistoryNoteFromEditModalFactory,
        HistoryNoteDeleteHistoryNoteFromListModalFactory $historyNoteDeleteHistoryNoteFromListModalFactory
    ) {
        $this->historyNoteDeleteHistoryNoteFromEditModalFactory = $historyNoteDeleteHistoryNoteFromEditModalFactory;
        $this->historyNoteDeleteHistoryNoteFromListModalFactory = $historyNoteDeleteHistoryNoteFromListModalFactory;
    }

    /**
     * @return HistoryNoteDeleteHistoryNoteFromEditModalFactory
     */
    public function getHistoryNoteDeleteHistoryNoteFromEditModalFactory()
    {
        return $this->historyNoteDeleteHistoryNoteFromEditModalFactory;
    }

    /**
     * @return HistoryNoteDeleteHistoryNoteFromListModalFactory
     */
    public function getHistoryNoteDeleteHistoryNoteFromListModalFactory()
    {
        return $this->historyNoteDeleteHistoryNoteFromListModalFactory;
    }
}