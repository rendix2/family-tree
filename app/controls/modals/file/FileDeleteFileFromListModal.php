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
use Nette\Utils\ArrayHash;
use Nette\Utils\FileSystem;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
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
     * @param int $fileId
     */
    public function handleFileDeleteFileFromList($fileId)
    {
        $presenter = $this->presenter;

        if ($presenter->isAjax()) {
            $fileModalItem = $this->fileManager->getByPrimaryKeyCached($fileId);

            $this['fileDeleteFileFromListForm']->setDefaults(['fileId' => $fileId]);

            $fileFilter = $this->fileFilter;

            $presenter->template->modalName = 'fileDeleteFileFromList';
            $presenter->template->fileModalItem = $fileFilter($fileModalItem);

            $presenter->payload->showModal = true;

            $presenter->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentFileDeleteFileFromListForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'fileDeleteFileFromListFormYesOnClick']);
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

        try {
            $file = $this->fileManager->getByPrimaryKey($values->fileId);

            $sep = DIRECTORY_SEPARATOR;

            $fileName = $this->fileDir . $file->newName . '.' . $file->extension;
            $thumbnailFileName = $this->fileDir . $sep . 'thumbnails' . $sep . $file->newName . '.' . $file->extension;

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
