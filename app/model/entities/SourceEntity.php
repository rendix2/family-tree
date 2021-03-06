<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceEntity.php
 * User: Tomáš Babický
 * Date: 15.11.2020
 * Time: 20:41
 */

namespace Rendix2\FamilyTree\App\Model\Entities;

/**
 * Class SourceEntity
 *
 * @property int _sourceTypeId
 * @property int _personId
 * @package Rendix2\FamilyTree\App\Model\Entities
 */
class SourceEntity implements IEntity
{
    use Entity;
    use PrimaryKey;

    /**
     * @var string $link
     */
    public $link;

    /**
     * @var PersonEntity $person
     */
    public $person;

    /**
     * @var SourceTypeEntity $sourceType
     */
    public $sourceType;
}
