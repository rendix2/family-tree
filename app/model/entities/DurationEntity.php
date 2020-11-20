<?php
/**
 *
 * Created by PhpStorm.
 * Filename: DurationEntity.php
 * User: Tomáš Babický
 * Date: 10.11.2020
 * Time: 23:45
 */

namespace Rendix2\FamilyTree\App\Model\Entities;

use Dibi\DateTime;

/**
 * Class DurationEntity
 *
 * @package Rendix2\FamilyTree\App\Model\Entities
 */
class DurationEntity
{
    /**
     * DurationEntity constructor.
     *
     * @param array $array
     */
    public function __construct(array $array)
    {
        $this->dateSince = $array['_dateSince'];
        $this->dateTo = $array['_dateTo'];
        $this->untilNow = (bool)$array['_untilNow'];
    }

    /**
     * @var DateTime
     */
    public $dateSince;

    /**
     * @var DateTime
     */
    public $dateTo;

    /**
     * @var bool $untilNow
     */
    public $untilNow;
}
