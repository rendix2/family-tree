<?php
/**
 *
 * Created by PhpStorm.
 * Filename: FileDeleteFileFromEditModal.php
 * User: Tomáš Babický
 * Date: 30.01.2021
 * Time: 17:17
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\File;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Nette\Utils\FileSystem;
use Rendix2\FamilyTree\App\Controls\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\DeleteModalFormSettings;
use Rendix2\FamilyTree\App\Filters\FileFilter;
use Rendix2\FamilyTree\App\Model\FileDir;
use Rendix2\FamilyTree\App\Model\Managers\FileManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Class FileDeleteFileFromEditModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\File
 */
class FileDeleteFileFromEditModal extends Control
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
     * FileDeleteFileFromEditModal constructor.
     *
     * @param DeleteModalForm $deleteModalForm
     * @param FileDir         $fileDir
     * @param FileFilter      $fileFilter
     * @param FileManager     $fileManager
     */
    public function __construct(
        DeleteModalForm $deleteModalForm,
        FileDir $fileDir,
        FileFilter $fileFilter,
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
        $this['fileDeleteFileFromEditForm']->render();
    }

    /**
     * @param int $fileId
     */
    public function handleFileDeleteFileFromEdit($fileId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('File:edit', $presenter->getParameter('id'));
        }

        $fileModalItem = $this->fileManager->select()->getManager()->getByPrimaryKey($fileId);

        $this['fileDeleteFileFromEditForm']->setDefaults(['fileId' => $fileId]);

        $fileFilter = $this->fileFilter;

        $presenter->template->modalName = 'fileDeleteFileFromEdit';
        $presenter->template->fileModalItem = $fileFilter($fileModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentFileDeleteFileFromEditForm()
    {
        $deleteModalFormSettings = new DeleteModalFormSettings();
        $deleteModalFormSettings->callBack = [$this, 'fileDeleteFileFromEditFormYesOnClick'];
        $deleteModalFormSettings->httpRedirect = true;

        $form = $this->deleteModalForm->create($deleteModalFormSettings);

        $form->addHidden('fileId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function fileDeleteFileFromEditFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('File:edit', $presenter->getParameter('id'));
        }

        try {
            $file = $this->fileManager->select()->getManager()->getByPrimaryKey($values->fileId);

            $sep = DIRECTORY_SEPARATOR;

            $fileName = $this->fileDir->getFileDir() . $file->newName . '.' . $file->extension;
            $thumbnailFileName = $this->fileDir->getFileDir() . $sep . 'thumbnails' . $sep . $file->newName . '.' . $file->extension;

            if (file_exists($fileName)) {
                FileSystem::delete($fileName);

                if (file_exists($thumbnailFileName)) {
                    FileSystem::delete($thumbnailFileName);
                }
            }

            $this->fileManager->delete()->deleteByPrimaryKey($values->fileId);

            $countries = $this->fileManager->select()->getCachedManager()->getAll();

            $presenter->template->countries = $countries;

            $presenter->flashMessage('file_deleted', BasePresenter::FLASH_SUCCESS);

            $presenter->redirect('File:default');
        } catch (ForeignKeyConstraintViolationException $e) {
            if ($e->getCode() === 1451) {
                $presenter->flashMessage('Item has some unset relations', BasePresenter::FLASH_DANGER);
            } else {
                Debugger::log($e, ILogger::EXCEPTION);
            }
        }
    }
}
