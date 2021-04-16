<?php
/**
 *
 * Created by PhpStorm.
 * Filename: CountryAddresdDeleteModal.php
 * User: Tomáš Babický
 * Date: 30.10.2020
 * Time: 1:14
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Country;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\DeleteModalFormSettings;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Model\Managers\AddressManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Class CountryDeleteAddressModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Country
 */
class CountryDeleteAddressModal extends Control
{
    /**
     * @var AddressFilter $addressFilter
     */
    private $addressFilter;

    /**
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * @var AddressManager $addressManager
     */
    private $addressManager;

    /**
     * @var DeleteModalForm $deleteModalForm
     */
    private $deleteModalForm;

    /**
     * CountryDeleteAddressModal constructor.
     *
     * @param AddressFilter   $addressFilter
     * @param AddressFacade   $addressFacade
     * @param AddressManager  $addressManager
     * @param DeleteModalForm $deleteModalForm
     */
    public function __construct(
        AddressFilter $addressFilter,
        AddressFacade $addressFacade,
        AddressManager $addressManager,
        DeleteModalForm $deleteModalForm
    ) {
        parent::__construct();

        $this->addressFilter = $addressFilter;
        $this->addressFacade = $addressFacade;
        $this->addressManager = $addressManager;

        $this->deleteModalForm = $deleteModalForm;
    }

    public function render()
    {
        $this['countryDeleteAddressForm']->render();
    }


    /**
     * @param int $addressId
     * @param int $countryId
     */
    public function handleCountryDeleteAddress($addressId, $countryId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Country:edit', $presenter->getParameter('id'));
        }

        $this['countryDeleteAddressForm']->setDefaults(
            [
                'addressId' => $addressId,
                'countryId' => $countryId
            ]
        );

        $addressFilter = $this->addressFilter;

        $addressModalItem = $this->addressFacade->select()->getCachedManager()->getByPrimaryKey($addressId);

        $presenter->template->modalName = 'countryDeleteAddress';
        $presenter->template->addressModalItem = $addressFilter($addressModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentCountryDeleteAddressForm()
    {
        $deleteModalFormSettings = new DeleteModalFormSettings();
        $deleteModalFormSettings->callBack = [$this, 'countryDeleteAddressFormYesOnClick'];

        $form = $this->deleteModalForm->create($deleteModalFormSettings);
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
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Country:edit', $presenter->getParameter('id'));
        }

        try {
            $this->addressManager->delete()->deleteByPrimaryKey($values->addressId);

            $addresses = $this->addressFacade->select()->getCachedManager()->getByCountryId($values->countryId);

            $presenter->template->addresses = $addresses;

            $presenter->payload->showModal = false;

            $presenter->flashMessage('address_deleted', BasePresenter::FLASH_SUCCESS);

            $presenter->redrawControl('addresses');
        } catch (ForeignKeyConstraintViolationException $e) {
            if ($e->getCode() === 1451) {
                $presenter->flashMessage('Item has some unset relations', BasePresenter::FLASH_DANGER);

            } else {
                Debugger::log($e, ILogger::EXCEPTION);
            }
        } finally {
            $presenter->redrawControl('flashes');
        }
    }
}
