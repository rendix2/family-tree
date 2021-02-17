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

use Nette\Application\Responses\FileResponse;
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
use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;
use Rendix2\FamilyTree\App\Model\Entities\FileEntity;
use Rendix2\FamilyTree\App\Model\Facades\FileFacade;
use Rendix2\FamilyTree\App\Model\FileHelper;
use Rendix2\FamilyTree\App\Presenters\Traits\File\FileDeleteFileFromListModal;
use Rendix2\FamilyTree\App\Presenters\Traits\File\FileDeleteFileFromEditModal;

/**
 * Class FilePresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class FilePresenter extends BasePresenter
{
    use FileDeleteFileFromListModal;
    use FileDeleteFileFromEditModal;

    /**
     * @var string $fileDir
     */
    private $fileDir;

    /**
     * @var FileFacade $fileFacade
     */
    private $fileFacade;

    /**
     * @var FileFilter $fileFilter
     */
    private $fileFilter;

    /**
     * @var FileManager $fileManager
     */
    private $fileManager;

    /**
     * @var PersonFilter $personFilter
     */
    private $personFilter;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var PersonSettingsManager $personSettingsManager
     */
    private $personSettingsManager;


    /**
     * FilePresenter constructor.
     *
     * @param FileFacade $fileFacade
     * @param FileFilter $fileFilter
     * @param FileManager $fileManager
     * @param PersonFilter $personFilter
     * @param PersonManager $personManager
     * @param PersonSettingsManager $personSettingsManager
     * @param Container $container
     */
    public function __construct(
        FileFacade $fileFacade,
        FileFilter $fileFilter,
        FileManager $fileManager,
        PersonFilter $personFilter,
        PersonManager $personManager,
        PersonSettingsManager $personSettingsManager,
        Container $container
    ) {
        parent::__construct();

        $this->fileFacade = $fileFacade;

        $this->fileFilter = $fileFilter;
        $this->personFilter = $personFilter;

        $this->fileManager = $fileManager;
        $this->personManager = $personManager;

        $this->personSettingsManager = $personSettingsManager;

        $this->fileDir = $container->getParameters()['wwwDir'] . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $files = $this->fileFacade->getAll();

        $this->template->files = $files;
        $this->template->addFilter('file', $this->fileFilter);
        $this->template->addFilter('person', $this->personFilter);
    }

    /**
     * @param int $id file ID
     */
    public function actionDownload($id)
    {
        $file = $this->fileFacade->getByPrimaryKeyCached($id);

        if (!$file) {
            $this->error('Item not found');
        }

        $filePath = $this->fileDir . $file->newName . '.' . $file->extension;
        $downloadedFileName = $file->originName . '.' . $file->extension;

        $fileResponse = new FileResponse($filePath, $downloadedFileName);

        $this->sendResponse($fileResponse);
    }

    /**
     * @param int $id file ID
     */
    public function actionEdit($id)
    {
        $persons = $this->personSettingsManager->getAllPairsCached($this->translator);

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
     * @param int $id file ID
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
        $this->template->addFilter('file', $this->fileFilter);
    }

    /**
     * @return Form
     */
    public function createComponentFileForm()
    {
        $formFactory = new FileForm($this->translator);

        $form = $formFactory->create();
        $form->onSuccess[] = [$this, 'fileFormSuccess'];

        return $form;
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
            $fileType = FileHelper::getFileType($extension);

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
