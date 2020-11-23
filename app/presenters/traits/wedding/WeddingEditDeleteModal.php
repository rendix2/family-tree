<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteAddressEditModal.php
 * User: Tomáš Babický
 * Date: 16.11.2020
 * Time: 21:12
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Wedding;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Trait GenusEditDeleteModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Wedding
 */
trait WeddingEditDeleteModal
{
    /**
     * @param int $weddingId
     */
    public function handleEditDeleteItem($weddingId)
    {
        if ($this->isAjax()) {
            $wedding = $this->weddingFacade->getByPrimaryKey($weddingId);

            $this['editDeleteForm']->setDefaults(['weddingId' => $weddingId]);

            $this->template->modalName = 'editDeleteItem';
            $this->template->weddingItem = $wedding;

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

        $form->addHidden('weddingId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function editDeleteFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        try {
            $this->weddingManager->deleteByPrimaryKey($values->weddingId);

            $this->flashMessage('wedding_was_deleted', self::FLASH_SUCCESS);
        } catch (ForeignKeyConstraintViolationException $e) {
            if ($e->getCode() === 1451) {
                $this->flashMessage('Item has some unset relations', self::FLASH_DANGER);
            } else {
                Debugger::log($e, ILogger::EXCEPTION);
            }
        }

        $this->redirect(':default');
    }
}
