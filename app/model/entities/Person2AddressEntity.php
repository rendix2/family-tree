<?php
/**
 *
 * Created by PhpStorm.
 * Filename: Person2AddressEntity.php
 * User: Tomáš Babický
 * Date: 10.11.2020
 * Time: 12:27
 */

namespace Rendix2\FamilyTree\App\Model\Entities;

/**
 * Class Person2AddressEntity
 *
 * @package Rendix2\FamilyTree\App\Model\Entities
 */
class Person2AddressEntity
{
    use Entity;

    /**
     * @var PersonEntity
     */
    public $person;

    /**
     * @var AddressEntity $address
     */
    public $address;

    /**
     * @var DurationEntity $duration
     */
    public $duration;
}
