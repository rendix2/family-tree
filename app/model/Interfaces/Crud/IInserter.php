<?php
/**
 *
 * Created by PhpStorm.
 * Filename: IInsert.php
 * User: Tomáš Babický
 * Date: 02.04.2021
 * Time: 20:51
 */

namespace Rendix2\FamilyTree\App\Model\Interfaces;

/**
 * Interface IInserter
 *
 * @package Rendix2\FamilyTree\App\Model\Interfaces
 */
interface IInserter
{
    public function insert(array $data);
}
