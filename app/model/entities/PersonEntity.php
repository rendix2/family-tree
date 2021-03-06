<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonEntity.php
 * User: Tomáš Babický
 * Date: 10.11.2020
 * Time: 12:27
 */

namespace Rendix2\FamilyTree\App\Model\Entities;

use Dibi\DateTime;

/**
 * Class PersonEntity
 *
 * @property int _birthTownId
 * @property int _deathTownId
 * @property int _gravedTownId
 * @property int _birthAddressId
 * @property int _deathAddressId
 * @property int _gravedAddressId
 * @property int _motherId
 * @property int _fatherId
 * @property int _genusId
 * @property bool hasChildren
 *
 * @package Rendix2\FamilyTree\App\Model\Entities
 */
class PersonEntity implements IEntity
{
    use Entity;
    use PrimaryKey;

    /**
     * @var string $gender
     */
    public $gender;

    /**
     * @var string $name
     */
    public $name;

    /**
     * @var string $nameFonetic
     */
    public $nameFonetic;

    /**
     * @var string $nameCall
     */
    public $nameCall;

    /**
     * @var string $surname
     */
    public $surname;

    /**
     * @var bool $hasBirthDate
     */
    public $hasBirthDate;

    /**
     * @var DateTime $birthDate
     */
    public $birthDate;

    /**
     * @var bool $hasBirthYear
     */
    public $hasBirthYear;

    public $birthYear;

    /**
     * @var bool $stillAlive
     */
    public $stillAlive;

    /**
     * @var bool $hasDeathDate
     */
    public $hasDeathDate;

    /**
     * @var DateTime $deathDate
     */
    public $deathDate;

    public $hasDeathYear;

    public $deathYear;

    /**
     * @var bool $hasAge
     */
    public $hasAge;

    /**
     * @var int $age
     */
    public $age;

    /**
     * @var PersonEntity $mother
     */
    public $mother;

    /**
     * @var PersonEntity $father
     */
    public $father;

    /**
     * @var GenusEntity $genus
     */
    public $genus;

    /**
     * @var TownEntity $birthTown
     */
    public $birthTown;

    /**
     * @var AddressEntity $birthAddress
     */
    public $birthAddress;

    /**
     * @var TownEntity $deathTown
     */
    public $deathTown;

    /**
     * @var AddressEntity $deathAddress
     */
    public $deathAddress;

    /**
     * @var TownEntity $gravedTown
     */
    public $gravedTown;

    /**
     * @var AddressEntity $gravedAddress
     */
    public $gravedAddress;

    /**
     * @var string $note
     */
    public $note;
}
