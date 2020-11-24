<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteAddressEditModal.php
 * User: Tomáš Babický
 * Date: 16.11.2020
 * Time: 21:12
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Source;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\SourceFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Trait SourceEditDeleteModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Source
 */
trait SourceEditDeleteModal
{
    /**
     * @param int $sourceId
     */
    public function handleEditDeleteItem($sourceId)
    {
        if ($this->isAjax()) {
            $source = $this->sourceFacade->getByPrimaryKey($sourceId);

            $this['editDeleteForm']->setDefaults(['sourceId' => $sourceId]);


            $sourceFilter = new SourceFilter();

            $sourceModalItem = $this->sourceFacade->getByPrimaryKeyCached($sourceId);

            $this->template->modalName = 'editDeleteItem';
            $this->template->sourceModalItem = $sourceFilter($sourceModalItem);

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
        
        $form = $formFactory->create($this, 'editDeleteFormOk', true);
        $form->addHidden('sourceId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function editDeleteFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        try {
            $this->sourceManager->deleteByPrimaryKey($values->sourceId);

            $this->flashMessage('source_was_deleted', self::FLASH_SUCCESS);

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
