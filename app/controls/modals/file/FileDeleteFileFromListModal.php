<?php
/**
 *
 * Created by PhpStorm.
 * Filename: FileDeleteFileFormListModal.php
 * User: Tomáš Babický
 * Date: 10.01.2021
 * Time: 14:41
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\File;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Nette\Utils\FileSystem;
use Rendix2\FamilyTree\App\Controls\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\DeleteModalFormSettings;
use Rendix2\FamilyTree\App\Filters\FileFilter;

use Rendix2\FamilyTree\App\Managers\FileManager;
use Rendix2\FamilyTree\App\Model\FileDir;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Class FileDeleteFileFromListModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\File
 */
class FileDeleteFileFromListModal extends Control
{
    /**
     * @var FileDir $fileDir
     */
    private $fileDir;

    /**
     * @var FileFilter $fileFilter
     */
    private $fileFilter;

    /**
     * @var DeleteModalForm $deleteModalForm
     */
    private $deleteModalForm;

    /**
     * @var FileManager $fileManager
     */
    private $fileManager;

    /**
     * FileDeleteFileFromListModal constructor.
     *
     * @param FileDir $fileDir
     * @param FileFilter $fileFilter
     * @param DeleteModalForm $deleteModalForm
     * @param FileManager $fileManager
     */
    public function __construct(
        FileDir $fileDir,

        FileFilter $fileFilter,

        DeleteModalForm $deleteModalForm,

        FileManager $fileManager
    ) {
        parent::__construct();

        $this->fileDir = $fileDir;

        $this->fileFilter = $fileFilter;

        $this->deleteModalForm = $deleteModalForm;

        $this->fileManager = $fileManager;
    }

    public function render()
    {
        $this['fileDeleteFileFromListForm']->render();
    }

    /**
     * @param int $fileId
     */
    public function handleFileDeleteFileFromList($fileId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('File:default');
        }

        $fileModalItem = $this->fileManager->getByPrimaryKeyCached($fileId);

        $this['fileDeleteFileFromListForm']->setDefaults(['fileId' => $fileId]);

        $fileFilter = $this->fileFilter;

        $presenter->template->modalName = 'fileDeleteFileFromList';
        $presenter->template->fileModalItem = $fileFilter($fileModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentFileDeleteFileFromListForm()
    {
        $deleteModalFormSettings = new DeleteModalFormSettings();
        $deleteModalFormSettings->callBack = [$this, 'fileDeleteFileFromListFormYesOnClick'];

        $form = $this->deleteModalForm->create($deleteModalFormSettings);

        $form->addHidden('fileId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function fileDeleteFileFromListFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('File:default');
        }

        try {
            $file = $this->fileManager->getByPrimaryKey($values->fileId);

            $sep = DIRECTORY_SEPARATOR;

            $fileName = $this->fileDir->getFileDir() . $file->newName . '.' . $file->extension;
            $thumbnailFileName = $this->fileDir->getFileDir() . $sep . 'thumbnails' . $sep . $file->newName . '.' . $file->extension;

            if (file_exists($fileName)) {
                FileSystem::delete($fileName);

                if (file_exists($thumbnailFileName)) {
                    FileSystem::delete($thumbnailFileName);
                }
            }

            $this->fileManager->deleteByPrimaryKey($values->fileId);

            $files = $this->fileManager->getAllCached();

            $presenter->template->files = $files;

            $presenter->flashMessage('file_deleted', BasePresenter::FLASH_SUCCESS);

            $presenter->redrawControl('list');
        } catch (ForeignKeyConstraintViolationException $e) {
            if ($e->getCode() === 1451) {
                $presenter->flashMessage('Item has some unset relations', BasePresenter::FLASH_DANGER);
            } else {
                Debugger::log($e, ILogger::EXCEPTION);
            }
        } finally {
            $presenter->redrawControl('flashes');
        }
    }
}
