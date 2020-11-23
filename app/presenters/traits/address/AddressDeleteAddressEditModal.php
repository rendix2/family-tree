<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteAddressEditModal.php
 * User: Tomáš Babický
 * Date: 16.11.2020
 * Time: 21:12
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Address;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Trait AddressDeleteAddressEditModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Address
 */
trait AddressDeleteAddressEditModal
{
    /**
     * @param int $addressId
     */
    public function handleEditDeleteItem($addressId)
    {
        if ($this->isAjax()) {
            $this['editDeleteForm']->setDefaults(['addressId' => $addressId]);

            $addressFilter = new AddressFilter();

            $addressModalItem = $this->addressFacade->getByPrimaryKey($addressId);

            $this->template->modalName = 'editDeleteItem';
            $this->template->addressModalItem = $addressFilter($addressModalItem);

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
        $form->addHidden('addressId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function editDeleteFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        try {
            $this->addressManager->deleteByPrimaryKey($values->addressId);

            $this->flashMessage('address_was_deleted', self::FLASH_SUCCESS);
        } catch (ForeignKeyConstraintViolationException $e) {
            if ($e->getCode() === 1451) {
                $this->flashMessage('Item has some unset relations', self::FLASH_DANGER);
            } else {
                Debugger::log($e, ILogger::EXCEPTION);
            }
        }

        $this->redirect('Address:default');
    }
}
