<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteWeddingAddressModal.php
 * User: Tomáš Babický
 * Date: 28.11.2020
 * Time: 1:09
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Address;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Filters\WeddingFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Trait AddressDeleteWeddingAddressModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Address
 */
trait AddressDeleteWeddingAddressModal
{
    /**
     * @param int $addressId
     * @param int $weddingId
     */
    public function handleDeleteWeddingAddressItem($addressId, $weddingId)
    {
        if ($this->isAjax()) {
            $this['deleteWeddingAddressForm']->setDefaults(
                [
                    'addressId' => $addressId,
                    'weddingId' => $weddingId
                ]
            );

            $weddingModalItem = $this->weddingFacade->getByPrimaryKeyCached($weddingId);
            $addressModalItem = $this->addressFacade->getByPrimaryKeyCached($addressId);

            $personFilter = new PersonFilter($this->getTranslator(), $this->getHttpRequest());
            $weddingFilter = new WeddingFilter($personFilter);
            $addressFilter = new AddressFilter();

            $this->template->modalName = 'deleteWeddingAddressItem';
            $this->template->weddingModalItem = $weddingFilter($weddingModalItem);
            $this->template->addressModalItem = $addressFilter($addressModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentDeleteWeddingAddressForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());
        $form = $formFactory->create($this, 'deleteWeddingAddressFormOk');

        $form->addHidden('addressId');
        $form->addHidden('weddingId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function deleteWeddingAddressFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        $this->weddingManager->updateByPrimaryKey($values->weddingId, ['addressId' => null]);

        $weddings = $this->weddingFacade->getByAddressId($values->addressId);

        $this->template->weddings = $weddings;

        $this->flashMessage('wedding_address_was_deleted', self::FLASH_SUCCESS);

        $this->payload->showModal = false;

        $this->redrawControl('flashes');
        $this->redrawControl('weddings');
    }
}
