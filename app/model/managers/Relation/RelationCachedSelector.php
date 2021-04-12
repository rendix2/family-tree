<?php
/**
 *
 * Created by PhpStorm.
 * Filename: RelationCachedSelector.php
 * User: Tomáš Babický
 * Date: 06.04.2021
 * Time: 21:48
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Relation;

use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultCachedSelector;

/**
 * Class RelationCachedSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Relation
 */
class RelationCachedSelector extends DefaultCachedSelector implements IRelationSelector
{
    /**
     * RelationCachedSelector constructor.
     *
     * @param IStorage         $storage
     * @param RelationSelector $selector
     */
    public function __construct(
        IStorage $storage,
        RelationSelector $selector
    ) {
        parent::__construct($storage, $selector);
    }

    public function getByMaleId($maleId)
    {
        return $this->getCache()->call([$this->getSelector(), 'getByMaleId'], $maleId);
    }

    public function getByFemaleId($femaleId)
    {
        return $this->getCache()->call([$this->getSelector(), 'getByFemaleId'], $femaleId);
    }

    public function getByMaleIdAndFemaleId($maleId, $femaleId)
    {
        return $this->getCache()->call([$this->getSelector(), 'getByMaleIdAndFemaleId'], $maleId, $femaleId);
    }
}
