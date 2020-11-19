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
 * @package Rendix2\FamilyTree\App\Model\Entities
 */
class GenusEntity
{
    use Construct;

    /**
     * @var int $id
     */
    public $id;

    /**
     * @var string $surname
     */
    public $surname;

    /**
     * @var string $surnameFonetic
     */
    public $surnameFonetic;
}
