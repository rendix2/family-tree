<?php
/**
 *
 * Created by PhpStorm.
 * Filename: NameAddGenusModalFactory.php
 * User: Tomáš Babický
 * Date: 23.03.2021
 * Time: 21:31
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Name\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Name\NameAddGenusModal;

/**
 * Interface NameAddGenusModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Name\Factory
 */
interface NameAddGenusModalFactory
{
    /**
     * @return NameAddGenusModal
     */
    public function create();
}
