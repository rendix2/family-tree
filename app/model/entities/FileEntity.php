<?php
/**
 *
 * Created by PhpStorm.
 * Filename: FileEntity.php
 * User: Tomáš Babický
 * Date: 15.12.2020
 * Time: 10:14
 */

namespace Rendix2\FamilyTree\App\Model\Entities;

/**
 * Class FileEntity
 *
 * @package Rendix2\FamilyTree\App\Model\Entities
 */
class FileEntity
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
     * @var string $originName
     */
    public $originName;

    /**
     * @var string $newName
     */
    public $newName;

    /**
     * @var string $extension
     */
    public $extension;

    /**
     * @var int $size
     */
    public $size;

    /**
     * @var string $description
     */
    public $description;

    /**
     * @var string $type
     */
    public $type;

}
