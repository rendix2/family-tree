<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownDeleteAddressModal.php
 * User: Tomáš Babický
 * Date: 31.10.2020
 * Time: 15:21
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Town;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Trait TownDeleteAddressModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Town
 */
trait TownDeleteAddressModal
{
    /**
     * @param int $townId
     * @param int $addressId
     */
    public function handleDeleteAddressItem($townId, $addressId)
    {
        if ($this->isAjax()) {
            $this['deleteTownAddressForm']->setDefaults(
                [
                    'addressId' => $addressId,
                    'townId' => $townId
                ]
            );

            $addressFilter = new AddressFilter();

            $addressModalItem = $this->addressFacade->getByPrimaryKeyCached($addressId);

            $this->template->modalName = 'deleteAddressItem';
            $this->template->addressModalItem = $addressFilter($addressModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentDeleteTownAddressForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());
        $form = $formFactory->create($this, 'deleteTownAddressFormOk');

        $form->addHidden('townId');
        $form->addHidden('addressId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function deleteTownAddressFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        if (!$this->isAjax()) {
            $this->redirect('Town:edit', $values->townId);
        }

        try {
            $this->addressManager->deleteByPrimaryKey($values->addressId);

            $addresses = $this->addressFacade->getByTownIdCached($values->townId);

            $this->template->addresses = $addresses;

            $this->payload->showModal = false;

            $this->flashMessage('address_was_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('addresses');
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
