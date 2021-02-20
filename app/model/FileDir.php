<?php
/**
 *
 * Created by PhpStorm.
 * Filename: FileDir.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 16:19
 */

namespace Rendix2\FamilyTree\App\Model;

use Nette\DI\Container;

/**
 * Class FileDir
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class FileDir
{
    /**
     * @var string $fileDir
     */
    private $fileDir;

    /**
     * FileDir constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->fileDir = $container->getParameters()['wwwDir'] . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR;
    }

    /**
     * @return string
     */
    public function getFileDir()
    {
        return $this->fileDir;
    }
}