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
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Nette\Utils\FileSystem;
use Rendix2\FamilyTree\App\Filters\FileFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Managers\FileManager;
use Rendix2\FamilyTree\App\Model\FileDir;
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
     * @var FileManager $fileManager
     */
    private $fileManager;

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * FileDeleteFileFromEditModal constructor.
     *
     * @param FileDir $fileDir
     * @param FileFilter $fileFilter
     * @param FileManager $fileManager
     * @param ITranslator $translator
     */
    public function __construct(
        FileDir $fileDir,
        FileFilter $fileFilter,
        FileManager $fileManager,
        ITranslator $translator
    ) {
        parent::__construct();

        $this->fileDir = $fileDir;
        $this->fileFilter = $fileFilter;
        $this->fileManager = $fileManager;
        $this->translator = $translator;
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

        if ($presenter->isAjax()) {
            $fileModalItem = $this->fileManager->getByPrimaryKeyCached($fileId);

            $this['fileDeleteFileFromEditForm']->setDefaults(['fileId' => $fileId]);

            $fileFilter = $this->fileFilter;

            $presenter->template->modalName = 'fileDeleteFileFromEdit';
            $presenter->template->fileModalItem = $fileFilter($fileModalItem);

            $presenter->payload->showModal = true;

            $presenter->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentFileDeleteFileFromEditForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'fileDeleteFileFromEditFormYesOnClick'], true);
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

            $countries = $this->fileManager->getAllCached();

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
