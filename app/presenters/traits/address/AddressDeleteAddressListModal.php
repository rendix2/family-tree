<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteAddressListModal.php
 * User: Tomáš Babický
 * Date: 16.11.2020
 * Time: 21:16
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
 * Trait AddressDeleteAddressListModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Address
 */
trait AddressDeleteAddressListModal
{
    /**
     * @param int $addressId
     */
    public function handleAddressDeleteAddressFromList($addressId)
    {
        if ($this->isAjax()) {
            $addressModalItem = $this->addressFacade->getByPrimaryKey($addressId);

            $this['addressDeleteListFromListForm']->setDefaults(['addressId' => $addressId]);

            $addressFiler = new AddressFilter();

            $this->template->modalName = 'addressDeleteAddressFromList';
            $this->template->addressModalItem = $addressFiler($addressModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentAddressDeleteListFromListForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());

        $form = $formFactory->create([$this, 'addressDeleteListFromListFormYesOnClick']);
        $form->addHidden('addressId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function addressDeleteListFromListFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        try {
            $this->addressManager->deleteByPrimaryKey($values->addressId);

            $this->flashMessage('address_was_deleted', self::FLASH_SUCCESS);

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
