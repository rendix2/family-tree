<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteHistoryNoteModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:09
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonDeleteHistoryNoteModal;

/**
 * Interface PersonDeleteHistoryNoteModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person\Factory
 */
interface PersonDeleteHistoryNoteModalFactory
{
    /**
     * @return PersonDeleteHistoryNoteModal
     */
    public function create();
}
