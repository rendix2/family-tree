<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteFileModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:09
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person;

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
 * Class PersonDeleteFileModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonDeleteFileModal extends Control
{
    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * @var FileManager $fileManager
     */
    private $fileManager;

    /**
     * @var string $fileDir
     */
    private $fileDir;

    /**
     * @var FileFilter $fileFilter
     */
    private $fileFilter;

    /**
     * PersonDeleteFileModal constructor.
     *
     * @param ITranslator $translator
     * @param FileManager $fileManager
     * @param FileDir $fileDir
     * @param FileFilter $fileFilter
     */
    public function __construct(
        ITranslator $translator,
        FileManager $fileManager,
        FileDir $fileDir,
        FileFilter $fileFilter
    ) {
        parent::__construct();

        $this->translator = $translator;
        $this->fileManager = $fileManager;
        $this->fileDir = $fileDir->getFileDir();
        $this->fileFilter = $fileFilter;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personDeleteFileForm']->render();
    }

    /**
     * @param int $fileId
     */
    public function handlePersonDeleteFile($fileId)
    {
        $presenter = $this->presenter;

        if ($presenter->isAjax()) {
            $fileModalItem = $this->fileManager->getByPrimaryKeyCached($fileId);

            $this['personDeleteFileForm']->setDefaults(['fileId' => $fileId]);

            $fileFilter = $this->fileFilter;

            $presenter->template->modalName = 'personDeleteFile';
            $presenter->template->fileModalItem = $fileFilter($fileModalItem);

            $presenter->payload->showModal = true;

            $presenter->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentPersonDeleteFileForm()
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
            $file = $this->fileManager->getByPrimaryKeyCached($values->fileId);

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

            $presenter->redrawControl('files');
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