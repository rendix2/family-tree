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

interface ICrud
{

    /**
     * @return ISelectRepository
     */
    public function select();

    public function insert();

    public function update();

    public function delete();
}
