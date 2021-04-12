<?php
/**
 *
 * Created by PhpStorm.
 * Filename: LagnaugeEntity.php
 * User: Tomáš Babický
 * Date: 05.04.2021
 * Time: 1:13
 */

namespace Rendix2\FamilyTree\App\Model\Entities;

/**
 * Class LanguageEntity
 *
 * @package Rendix2\FamilyTree\App\Model\Entities
 */
class LanguageEntity implements IEntity
{
    use Entity;
    use PrimaryKey;

    /**
     * @var string $langName
     */
    public $langName;
}
