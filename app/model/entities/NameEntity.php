<?php
/**
 *
 * Created by PhpStorm.
 * Filename: NameEntity.php
 * User: Tomáš Babický
 * Date: 11.11.2020
 * Time: 18:07
 */

namespace Rendix2\FamilyTree\App\Model\Entities;

/**
 * Class NameEntity
 *
 * @package Rendix2\FamilyTree\App\Model\Entities
 */
class NameEntity implements IEntity
{
    use Entity;
    use PrimaryKey;

    /**
     * @var PersonEntity $person
     */
    public $person;

    /**
     * @var GenusEntity $genus
     */
    public $genus;

    /**
     * @var string $name
     */
    public $name;

    /**
     * @var string $nameFonetic
     */
    public $nameFonetic;

    /**
     * @var string $surname
     */
    public $surname;

    /**
     * @var DurationEntity $duration
     */
    public $duration;
}
