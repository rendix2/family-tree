<?php
/**
 *
 * Created by PhpStorm.
 * Filename: WeddingEntity.php
 * User: Tomáš Babický
 * Date: 11.11.2020
 * Time: 1:10
 */

namespace Rendix2\FamilyTree\App\Model\Entities;

/**
 * Class WeddingEntity
 *
 * @package Rendix2\FamilyTree\App\Model\Entities
 */
class WeddingEntity
{
    use Entity;

    /**
     * @var int $id
     */
    public $id;

    /**
     * @var PersonEntity $husband
     */
    public $husband;

    /**
     * @var PersonEntity $wife
     */
    public $wife;

    /**
     * @var DurationEntity $duration
     */
    public $duration;

    /**
     * @var TownEntity $town
     */
    public $town;

    /**
     * @var AddressEntity $address
     */
    public $address;
}
