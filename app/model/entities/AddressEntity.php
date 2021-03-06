<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressEntity.php
 * User: Tomáš Babický
 * Date: 10.11.2020
 * Time: 3:30
 */

namespace Rendix2\FamilyTree\App\Model\Entities;

/**
 * Class AddressEntity
 *
 * @property int _townId
 * @package Rendix2\FamilyTree\App\Model\Entities
 */
class AddressEntity implements IEntity
{
    use Entity;
    use PrimaryKey;

    /**
     * @var string $street
     */
    public $street;

    /**
     * @var int $streetNumber
     */
    public $streetNumber;

    /**
     * @var int $houseNumber
     */
    public $houseNumber;

    /**
     * @var string $gps
     */
    public $gps;

    /**
     * @var TownEntity $town
     */
    public $town;
}
