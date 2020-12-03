<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteAddressEditModal.php
 * User: Tomáš Babický
 * Date: 16.11.2020
 * Time: 21:12
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\SourceType;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\SourceTypeFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Trait SourceEditDeleteModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\SourceType
 */
trait SourceTypeEditDeleteModal
{
    /**
     * @param int $sourceTypeId
     */
    public function handleEditDelete($sourceTypeId)
    {
        if ($this->isAjax()) {
            $this['editDeleteForm']->setDefaults(['sourceTypeId' => $sourceTypeId]);

            $sourceTypeModalItem = $this->sourceTypeManager->getByPrimaryKey($sourceTypeId);

            $sourceTypeFilter = new SourceTypeFilter();

            $this->template->modalName = 'editDelete';
            $this->template->sourceTypeModalItem = $sourceTypeFilter($sourceTypeModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentEditDeleteForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());
        $form = $formFactory->create([$this, 'editDeleteFormYesOnClick'], true);

        $form->addHidden('sourceTypeId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function editDeleteFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        try {
            $this->sourceTypeManager->deleteByPrimaryKey($values->sourceTypeId);

            $this->flashMessage('source_type_was_deleted', self::FLASH_SUCCESS);

            $this->redirect(':default');
        } catch (ForeignKeyConstraintViolationException $e) {
            if ($e->getCode() === 1451) {
                $this->flashMessage('Item has some unset relations', self::FLASH_DANGER);

                $this->redrawControl('flashes');
            } else {
                Debugger::log($e, ILogger::EXCEPTION);
            }
        }


    }
}
