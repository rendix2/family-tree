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
use Rendix2\FamilyTree\App\Controls\Forms\FileForm;
use Rendix2\FamilyTree\App\Controls\Modals\File\Container\FileModalContainer;
use Rendix2\FamilyTree\App\Controls\Modals\File\FileDeleteFileFromEditModal;
use Rendix2\FamilyTree\App\Controls\Modals\File\FileDeleteFileFromListModal;
use Rendix2\FamilyTree\App\Managers\FileManager;
use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;
use Rendix2\FamilyTree\App\Model\Entities\FileEntity;
use Rendix2\FamilyTree\App\Model\Facades\FileFacade;
use Rendix2\FamilyTree\App\Model\FileHelper;

/**
 * Class FilePresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class FilePresenter extends BasePresenter
{
    /**
     * @var string $fileDir
     */
    private $fileDir;

    /**
     * @var FileFacade $fileFacade
     */
    private $fileFacade;

    /**
     * @var FileManager $fileManager
     */
    private $fileManager;

    /**
     * @var FileModalContainer $fileModalContainer
     */
    private $fileModalContainer;

    /**
     * @var FileForm $fileForm
     */
    private $fileForm;

    /**
     * @var PersonSettingsManager $personSettingsManager
     */
    private $personSettingsManager;


    /**
     * FilePresenter constructor.
     *
     * @param FileFacade            $fileFacade
     * @param FileForm              $fileForm
     * @param FileManager           $fileManager
     * @param FileModalContainer    $fileModalContainer
     * @param PersonSettingsManager $personSettingsManager
     * @param Container             $container
     */
    public function __construct(
        FileFacade $fileFacade,
        FileForm $fileForm,
        FileManager $fileManager,
        FileModalContainer $fileModalContainer,
        PersonSettingsManager $personSettingsManager,
        Container $container
    ) {
        parent::__construct();

        $this->fileModalContainer = $fileModalContainer;

        $this->fileFacade = $fileFacade;
        $this->fileForm = $fileForm;

        $this->fileManager = $fileManager;

        $this->personSettingsManager = $personSettingsManager;

        $this->fileDir = $container->getParameters()['wwwDir'] . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $files = $this->fileFacade->getAllCached();

        $this->template->files = $files;
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
    }

    /**
     * @return Form
     */
    public function createComponentFileForm()
    {
        $form = $this->fileForm->create();

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

    /**
     * @return FileDeleteFileFromListModal
     */
    public function createComponentFileDeleteFileFromListModal()
    {
        return $this->fileModalContainer->getFileDeleteFileFromListModalFactory()->create();
    }

    /**
     * @return FileDeleteFileFromEditModal
     */
    public function createComponentFileDeleteFileFromEditModal()
    {
        return $this->fileModalContainer->getFileDeleteFileFromEditModalFactory()->create();
    }
}
