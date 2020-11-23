<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteAddressListModal.php
 * User: Tomáš Babický
 * Date: 16.11.2020
 * Time: 21:16
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
 * Trait AddressDeleteAddressListModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Wedding
 */
trait NameListDeleteModal
{
    /**
     * @param int $weddingId
     */
    public function handleListDeleteItem($weddingId)
    {
        if ($this->isAjax()) {
            $wedding = $this->weddingFacade->getByPrimaryKey($weddingId);

            $this['listDeleteForm']->setDefaults(['weddingId' => $weddingId]);

            $this->template->modalName = 'listDeleteItem';
            $this->template->weddingItem = $wedding;

            $this->payload->showModal = true;
            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentListDeleteForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());
        $form = $formFactory->create($this, 'listDeleteFormOk');

        $form->addHidden('weddingId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function listDeleteFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        try {
            $this->weddingManager->deleteByPrimaryKey($values->weddingId);

            $this->flashMessage('wedding_was_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('modal');
            $this->redrawControl('flashes');
            $this->redrawControl('list');
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