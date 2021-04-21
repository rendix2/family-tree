<?php
/**
 *
 * Created by PhpStorm.
 * Filename: ICrud.php
 * User: Tomáš Babický
 * Date: 02.04.2021
 * Time: 20:43
 */

namespace Rendix2\FamilyTree\App\Model\Interfaces;

/**
 * Interface ICrud
 *
 * @package Rendix2\FamilyTree\App\Model\Interfaces
 */
interface ICrud
{
    /**
     * @return ISelectRepository
     */
    public function select();

    /**
     * @return IInserter
     */
    public function insert();

    /**
     * @return IUpdater
     */
    public function update();

    /**
     * @return IDeleter
     */
    public function delete();
}
