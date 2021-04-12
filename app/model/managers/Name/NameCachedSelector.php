<?php
/**
 *
 * Created by PhpStorm.
 * Filename: NameCachedSeleector.php
 * User: Tomáš Babický
 * Date: 06.04.2021
 * Time: 22:55
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Name;

use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultCachedSelector;
use Rendix2\FamilyTree\App\Model\Managers\Name\Interfaces\INameSelector;

/**
 * Class NameCachedSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Name
 */
class NameCachedSelector extends DefaultCachedSelector implements INameSelector
{
    /**
     * NameCachedSelector constructor.
     *
     * @param IStorage     $storage
     * @param NameSelector $selector
     */
    public function __construct(
        IStorage $storage,
        NameSelector $selector
    ) {
        parent::__construct($storage, $selector);
    }

    public function getByPersonId($personId)
    {
        return $this->getCache()->call([$this->getSelector(), 'getByPersonId'], $personId);
    }

    public function getByGenusId($genusId)
    {
        return $this->getCache()->call([$this->getSelector(), 'getByGenusId'], $genusId);
    }
}
