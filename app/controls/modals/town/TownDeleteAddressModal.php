<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownDeleteAddressModal.php
 * User: Tomáš Babický
 * Date: 31.10.2020
 * Time: 15:21
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Town;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Class TownDeleteAddressModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Town
 */
class TownDeleteAddressModal extends Control
{
    /**
     * @param int $townId
     * @param int $addressId
     */
    public function handleTownDeleteAddress($townId, $addressId)
    {
        $presenter = $this->presenter;

        if ($presenter->isAjax()) {
            $this['townDeleteAddressForm']->setDefaults(
                [
                    'addressId' => $addressId,
                    'townId' => $townId
                ]
            );

            $addressFilter = $this->addressFilter;

            $addressModalItem = $this->addressFacade->getByPrimaryKeyCached($addressId);

            $presenter->template->modalName = 'townDeleteAddress';
            $presenter->template->addressModalItem = $addressFilter($addressModalItem);

            $presenter->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentTownDeleteAddressForm()
    {
        $formFactory = new DeleteModalForm($this->translator);
        $form = $formFactory->create([$this, 'townDeleteAddressFormYesOnClick']);

        $form->addHidden('townId');
        $form->addHidden('addressId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function townDeleteAddressFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $this->redirect('Town:edit', $values->townId);
        }

        try {
            $this->addressManager->deleteByPrimaryKey($values->addressId);

            $addresses = $this->addressFacade->getByTownIdCached($values->townId);

            $presenter->template->addresses = $addresses;

            $presenter->payload->showModal = false;

            $this->flashMessage('address_deleted', BasePresenter::FLASH_SUCCESS);

            $this->redrawControl('addresses');
        } catch (ForeignKeyConstraintViolationException $e) {
            if ($e->getCode() === 1451) {
                $this->flashMessage('Item has some unset relations', BasePresenter::FLASH_DANGER);
            } else {
                Debugger::log($e, ILogger::EXCEPTION);
            }
        } finally {
            $this->redrawControl('flashes');
        }
    }
}
