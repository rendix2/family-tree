<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceTypyEnity.php
 * User: Tomáš Babický
 * Date: 15.11.2020
 * Time: 20:41
 */

namespace Rendix2\FamilyTree\App\Model\Entities;

/**
 * Class SourceTypeEntity
 *
 * @package Rendix2\FamilyTree\App\Model\Entities
 */
class SourceTypeEntity implements IEntity
{
    use Entity;
    use PrimaryKey;

    /**
     * @var string $name
     */
    public $name;
}
