<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceCachedSelector.php
 * User: Tomáš Babický
 * Date: 06.04.2021
 * Time: 2:52
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Source;

use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultCachedSelector;
use Rendix2\FamilyTree\App\Model\Managers\Source\Interfaces\ISourceSelector;

/**
 * Class SourceCachedSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Source
 */
class SourceManagerCachedSelector extends DefaultCachedSelector implements ISourceSelector
{
    /**
     * SourceCachedSelector constructor.
     *
     * @param IStorage              $storage
     * @param SourceManagerSelector $selector
     */
    public function __construct(
        IStorage $storage,
        SourceManagerSelector $selector
    ) {
        parent::__construct($storage, $selector);
    }

    public function getByPersonId($personId)
    {
        return $this->getCache()->call([$this->getSelector(), 'getByPersonId'], $personId);
    }

    public function getBySourceTypeId($sourceTypeId)
    {
        return $this->getCache()->call([$this->getSelector(), 'getBySourceTypeId'], $sourceTypeId);
    }
}
