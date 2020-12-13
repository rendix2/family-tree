<?php
/**
 *
 * Created by PhpStorm.
 * Filename: HistoryNoteEntity.php
 * User: Tomáš Babický
 * Date: 16.11.2020
 * Time: 1:17
 */

namespace Rendix2\FamilyTree\App\Model\Entities;

use Dibi\DateTime;

/**
 * Class HistoryNoteEntity
 *
 * @package Rendix2\FamilyTree\App\Model\Entities
 */
class HistoryNoteEntity
{
    use Entity;

    /**
     * @var int $id
     */
    public $id;

    /**
     * @var PersonEntity $person
     */
    public $person;

    /**
     * @var string $text
     */
    public $text;

    /**
     * @var DateTime $date
     */
    public $date;
}
