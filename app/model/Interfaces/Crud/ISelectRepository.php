<?php
/**
 *
 * Created by PhpStorm.
 * Filename: ISelectRepository.php
 * User: Tomáš Babický
 * Date: 03.04.2021
 * Time: 1:00
 */

namespace Rendix2\FamilyTree\App\Model\Interfaces;


interface ISelectRepository
{

    public function getManager();

    public function getCachedManager();

}