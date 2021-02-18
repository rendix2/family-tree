<?php
/**
 *
 * Created by PhpStorm.
 * Filename: CountryAddresdDeleteModal.php
 * User: Tomáš Babický
 * Date: 30.10.2020
 * Time: 1:14
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Country;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Trait CountryDeleteAddressModal
 *
 * @package Nette\PhpGenerator\Traits\Country
 */
trait CountryDeleteAddressModal
{
    /**
     * @param int $addressId
     * @param int $countryId
     */
    public function handleCountryDeleteAddress($addressId, $countryId)
    {
        if ($this->isAjax()) {
            $this['countryDeleteAddressForm']->setDefaults(
                [
                    'addressId' => $addressId,
                    'countryId' => $countryId
                ]
            );

            $addressFilter = $this->addressFilter;

            $addressModalItem = $this->addressFacade->getByPrimaryKeyCached($addressId);

            $this->template->modalName = 'countryDeleteAddress';
            $this->template->addressModalItem = $addressFilter($addressModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentCountryDeleteAddressForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'countryDeleteAddressFormYesOnClick']);
        $form->addHidden('addressId');
        $form->addHidden('countryId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function countryDeleteAddressFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            try {
                $this->addressManager->deleteByPrimaryKey($values->addressId);

                $addresses = $this->addressFacade->getByCountryIdCached($values->countryId);

                $this->template->addresses = $addresses;

                $this->payload->showModal = false;

                $this->flashMessage('address_deleted', self::FLASH_SUCCESS);

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
        } else {
            $this->redirect('Country:edit', $values->countryId);
        }
    }
}
