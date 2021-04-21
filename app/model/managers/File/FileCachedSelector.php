<?php
/**
 *
 * Created by PhpStorm.
 * Filename: FileCachedSelector.php
 * User: Tomáš Babický
 * Date: 04.04.2021
 * Time: 20:13
 */

namespace Rendix2\FamilyTree\App\Model\Managers\File;

use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultCachedSelector;
use Rendix2\FamilyTree\App\Model\Managers\File\Interfaces\IFileSelector;

/**
 * Class FileCachedSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\File
 * @method FileSelector getSelector()
 */
class FileCachedSelector extends DefaultCachedSelector implements IFileSelector
{
    /**
     * FileCachedSelector constructor.
     *
     * @param IStorage       $storage
     * @param FileSelector $selector
     */
    public function __construct(
        IStorage $storage,
        FileSelector $selector
    ) {
        parent::__construct($storage, $selector);
    }

    public function getByPersonId($personId)
    {
        return $this->getCache()->call([$this->getSelector(), 'getByPersonId'], $personId);
    }
}
