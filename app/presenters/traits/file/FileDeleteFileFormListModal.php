<?php
/**
 *
 * Created by PhpStorm.
 * Filename: FileDeleteFileFormListModal.php
 * User: Tomáš Babický
 * Date: 10.01.2021
 * Time: 14:41
 */

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\FileFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Trait FileDeleteFileFormListModal
 */
trait FileDeleteFileFormListModal
{
    /**
     * @param int $fileId
     */
    public function handleFileDeleteFileFromList($fileId)
    {
        if ($this->isAjax()) {
            $fileModalItem = $this->fileManager->getByPrimaryKeyCached($fileId);

            $this['fileDeleteFileFromListForm']->setDefaults(['fileId' => $fileId]);

            $fileFilter = new FileFilter();

            $this->template->modalName = 'fileDeleteFileFromList';
            $this->template->fileModalItem = $fileFilter($fileModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentFileDeleteFileFromListForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());

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
        try {
            $this->fileManager->deleteByPrimaryKey($values->fileId);

            $countries = $this->fileManager->getAll();

            $this->template->countries = $countries;

            $this->flashMessage('file_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('list');
        } catch (ForeignKeyConstraintViolationException $e) {
            if ($e->getCode() === 1451) {
                $this->flashMessage('Item has some unset relations', self::FLASH_DANGER);
            } else {
                Debugger::log($e, ILogger::EXCEPTION);
            }
        } finally {
            $this->redrawControl('flashes');
        }
    }
}
