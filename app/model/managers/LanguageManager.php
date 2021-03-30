<?php
/**
 *
 * Created by PhpStorm.
 * Filename: LanguageManager.php
 * User: Tomáš Babický
 * Date: 08.09.2020
 * Time: 18:35
 */

namespace Rendix2\FamilyTree\App\Managers;

use Dibi\Fluent;
use Dibi\Row;
use Nette\NotImplementedException;

/**
 * Class LanguageManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class LanguageManager extends CrudManager
{
    /**
     * @return Row[]
     */
    public function getAll()
    {
        return $this->getAllFluent()
            ->fetchAll();
    }

    /**
     * @param Fluent $query
     */
    public function getBySubQuery(Fluent $query)
    {
        throw new NotImplementedException();
    }
}
