<?php
/**
 *
 * Created by PhpStorm.
 * Filename: FileModalContainer.php
 * User: Tomáš Babický
 * Date: 23.03.2021
 * Time: 21:23
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\File\Container;

use Rendix2\FamilyTree\App\Controls\Modals\File\Factory\FileDeleteFileFromEditModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\File\Factory\FileDeleteFileFromListModalFactory;

/**
 * Class FileModalContainer
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\File\Container
 */
class FileModalContainer
{
    /**
     * @var FileDeleteFileFromEditModalFactory $fileDeleteFileFromEditModalFactory
     */
    private $fileDeleteFileFromEditModalFactory;

    /**
     * @var FileDeleteFileFromListModalFactory $fileDeleteFileFromListModalFactory
     */
    private $fileDeleteFileFromListModalFactory;

    /**
     * FileModalContainer constructor.
     *
     * @param FileDeleteFileFromEditModalFactory $fileDeleteFileFromEditModalFactory
     * @param FileDeleteFileFromListModalFactory $fileDeleteFileFromListModalFactory
     */
    public function __construct(
        FileDeleteFileFromEditModalFactory $fileDeleteFileFromEditModalFactory,
        FileDeleteFileFromListModalFactory $fileDeleteFileFromListModalFactory
    ) {
        $this->fileDeleteFileFromEditModalFactory = $fileDeleteFileFromEditModalFactory;
        $this->fileDeleteFileFromListModalFactory = $fileDeleteFileFromListModalFactory;
    }

    /**
     * @return FileDeleteFileFromEditModalFactory
     */
    public function getFileDeleteFileFromEditModalFactory()
    {
        return $this->fileDeleteFileFromEditModalFactory;
    }

    /**
     * @return FileDeleteFileFromListModalFactory
     */
    public function getFileDeleteFileFromListModalFactory()
    {
        return $this->fileDeleteFileFromListModalFactory;
    }
}
