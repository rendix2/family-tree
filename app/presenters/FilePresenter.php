<?php
/**
 *
 * Created by PhpStorm.
 * Filename: FilePresenter.php
 * User: Tomáš Babický
 * Date: 15.12.2020
 * Time: 10:12
 */

namespace Rendix2\FamilyTree\App\Presenters;

use Nette\Application\UI\Form;
use Nette\DI\Container;
use Nette\Utils\ArrayHash;
use Nette\Utils\Image;
use Nette\Utils\Random;
use Rendix2\FamilyTree\App\Filters\FileFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\FileForm;
use Rendix2\FamilyTree\App\Managers\FileManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Model\Entities\FileEntity;
use Rendix2\FamilyTree\App\Model\Facades\FileFacade;

/**
 * Class FilePresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class FilePresenter extends BasePresenter
{
    /**
     * @var FileFacade $fileFacade
     */
    private $fileFacade;

    /**
     * @var FileManager $fileManager
     */
    private $fileManager;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var string $fileDir
     */
    private $fileDir;

    /**
     * FilePresenter constructor.
     *
     * @param FileFacade $fileFacade
     * @param FileManager $fileManager
     * @param PersonManager $personManager
     */
    public function __construct(
        FileFacade $fileFacade,
        FileManager $fileManager,
        PersonManager $personManager,
        Container $container
    ) {
        parent::__construct();

        $this->fileFacade = $fileFacade;
        $this->fileManager = $fileManager;
        $this->personManager = $personManager;
        $this->fileDir = $container->getParameters()['wwwDir'] . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $files = $this->fileFacade->getAll();

        $this->template->files = $files;
        $this->template->addFilter('file', new FileFilter());
        $this->template->addFilter('person', new PersonFilter($this->getTranslator(), $this->getHttpRequest()));
    }

    /**
     * @param int $id
     */
    public function actionEdit($id)
    {
        $persons = $this->personManager->getAllPairs($this->getTranslator());

        $this['fileForm-personId']->setItems($persons);

        if ($id) {
            $file = $this->fileFacade->getByPrimaryKeyCached($id);

            if (!$file) {
                $this->error('Item not found.');
            }

            $this['fileForm-personId']->setDefaultValue($file->person->id);
            $this['fileForm']->setDefaults((array)$file);
        }
    }

    /**
     * @param int $id
     */
    public function renderEdit($id)
    {
        if ($id) {
            $file = $this->fileFacade->getByPrimaryKeyCached($id);
        } else {
          $file = new FileEntity([]);
        }

        $this->template->fileEntity = $file;
        $this->template->fileDir = $this->fileDir;
        $this->template->fileEntity = $file;
        $this->template->addFilter('file', new FileFilter());
    }

    /**
     * @return Form
     */
    public function createComponentFileForm()
    {
        $formFactory = new FileForm($this->getTranslator());

        $form = $formFactory->create();
        $form->onSuccess[] = [$this, 'fileFormSuccess'];

        return $form;
    }

    /**
     * @param string $extension
     *
     * @return string
     */
    private function getFileType($extension)
    {
        switch ($extension)
        {
            case 'jpg':
            case 'jpeg':
            case 'gif':
            case 'png':
            case 'tif':
            case 'bmp':
                return 'image';

            case 'rtf':
            case 'txt':
                return 'text';

            case 'doc':
            case 'docx':
                return 'word';

            case 'pdf':
                return 'pdf';

            case 'xlsx':
            case 'xlsm':
            case 'xlsb':
            case 'xltx':
            case 'xls':
            case 'xlt':
                return 'excel';

            case 'pptx':
            case 'pptm':
            case 'ppt':
            case 'xps':
            case 'potx':
            case 'potm':
            case 'pot':
            case 'thmx':
            case 'ppsx':
            case 'ppsm':
            case 'pps':
            case 'ppam':
            case 'ppa':
            case 'odp':
                return 'powerpoint';

            case '7z':
            case 'zip':
            case 'rar':
            case 'tar':
                return 'archive';

            default:
                return 'unknown';
        }
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function fileFormSuccess(Form $form, ArrayHash $values)
    {
        if ($values->file->hasFile()) {
            $sep = DIRECTORY_SEPARATOR;

            $originFileName = $values->file->getName();
            $explodedName = explode('.', $originFileName);
            $explodedCount = count($explodedName);
            $extension = $explodedName[$explodedCount - 1];
            $originName = str_replace('.' . $extension, '', $originFileName);

            $generatedName = Random::generate();
            $newFileName = $generatedName . '.' . $extension;
            $newPath = $this->fileDir . $newFileName;
            $fileType = $this->getFileType($extension);

            $values->file->move($newPath);

            // crete thumbnail
            if ($fileType === 'image') {
                $image = Image::fromFile($newPath);

                $image->resize(400, 400);

                $thumbnailPath = $this->fileDir . 'thumbnails' . $sep . $newFileName;

                $image->save($thumbnailPath);

                $image->resize(120, 160);

                $thumbnailPath = $this->fileDir . 'thumbnails' . $sep . 's'. $newFileName;

                $image->save($thumbnailPath);
            }

            $data = [
                'personId' => $values->personId,
                'originName' => $originName,
                'newName' => $generatedName,
                'extension' => $extension,
                'type' => $fileType,
                'size' => $values->file->getSize(),
                'description' => $values->description
            ];
        } else {
            $data = [
                'personId' => $values->personId,
                'description' => $values->description,
            ];
        }

        $id = $this->getParameter('id');

        if ($id) {
            $this->fileManager->updateByPrimaryKey($id, $data);

            $this->flashMessage('file_saved', self::FLASH_SUCCESS);
        } else {
            $id = $this->fileManager->add($data);

            $this->flashMessage('file_added', self::FLASH_SUCCESS);
        }

        $this->redirect('File:edit', $id);
    }
}
