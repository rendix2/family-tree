<?php
/**
 *
 * Created by PhpStorm.
 * Filename: GenusEntity.php
 * User: Tomáš Babický
 * Date: 10.11.2020
 * Time: 13:10
 */

namespace Rendix2\FamilyTree\App\Model\Entities;

/**
 * Class GenusEntity
 *
 * @property int _personId
 * @package Rendix2\FamilyTree\App\Model\Entities
 */
class GenusEntity implements IEntity
{
    use Entity;
    use PrimaryKey;

    /**
     * @var string $surname
     */
    public $surname;

    /**
     * @var string $surnameFonetic
     */
    public $surnameFonetic;
}
