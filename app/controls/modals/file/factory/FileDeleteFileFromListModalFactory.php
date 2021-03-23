<?php
/**
 *
 * Created by PhpStorm.
 * Filename: FileDeleteFileFromListModalFactory.php
 * User: Tomáš Babický
 * Date: 23.03.2021
 * Time: 21:24
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\File\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\File\FileDeleteFileFromListModal;

/**
 * Interface FileDeleteFileFromListModalFactory
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\File\Factory
 */
interface FileDeleteFileFromListModalFactory
{
    /**
     * @return FileDeleteFileFromListModal
     */
    public function create();
}
