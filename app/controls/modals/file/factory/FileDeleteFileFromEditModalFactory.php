<?php
/**
 *
 * Created by PhpStorm.
 * Filename: FileDeleteFileFromEditModalFactory.php
 * User: Tomáš Babický
 * Date: 23.03.2021
 * Time: 21:24
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\File\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\File\FileDeleteFileFromEditModal;

/**
 * Interface FileDeleteFileFromEditModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\File\Factory
 */
interface FileDeleteFileFromEditModalFactory
{
    /**
     * @return FileDeleteFileFromEditModal
     */
    public function create();
}
