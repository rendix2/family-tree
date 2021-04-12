<?php
/**
 *
 * Created by PhpStorm.
 * Filename: IHistoryNoteSelector.php
 * User: Tomáš Babický
 * Date: 04.04.2021
 * Time: 23:28
 */

namespace Rendix2\FamilyTree\App\Model\Managers\HistoryNote\Interfaces;

use Rendix2\FamilyTree\App\Model\Interfaces\ISelector;

/**
 * Interface IHistoryNoteSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\HistoryNote\Interfaces
 */
interface IHistoryNoteSelector extends ISelector
{

    public function getByPersonId($personId);
}
