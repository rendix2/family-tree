<?php
/**
 *
 * Created by PhpStorm.
 * Filename: NameDeleteNameFromEditModalFactory.php
 * User: Tomáš Babický
 * Date: 23.03.2021
 * Time: 21:32
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Name\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Name\NameDeleteNameFromEditModal;

/**
 * Interface NameDeleteNameFromEditModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Name\Factory
 */
interface NameDeleteNameFromEditModalFactory
{
    /**
     * @return NameDeleteNameFromEditModal
     */
    public function create();
}
