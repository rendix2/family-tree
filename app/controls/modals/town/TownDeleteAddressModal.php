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
use Rendix2\FamilyTree\App\Controls\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\DeleteModalFormSettings;
use Rendix2\FamilyTree\App\Filters\AddressFilter;

use Rendix2\FamilyTree\App\Managers\AddressManager;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
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
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * @var AddressFilter $addressFilter
     */
    private $addressFilter;

    /**
     * @var AddressManager $addressManager
     */
    private $addressManager;

    /**
     * @var DeleteModalForm $deleteModalForm
     */
    private $deleteModalForm;

    /**
     * TownDeleteAddressModal constructor.
     *
     * @param AddressFacade   $addressFacade
     * @param AddressFilter   $addressFilter
     * @param DeleteModalForm $deleteModalForm
     * @param AddressManager  $addressManager
     */
    public function __construct(
        AddressFacade $addressFacade,
        AddressFilter $addressFilter,

        DeleteModalForm $deleteModalForm,

        AddressManager $addressManager
    ) {
        parent::__construct();

        $this->deleteModalForm = $deleteModalForm;

        $this->addressFacade = $addressFacade;
        $this->addressFilter = $addressFilter;
        $this->addressManager = $addressManager;
    }

    public function render()
    {
        $this['townDeleteAddressForm']->render();
    }

    /**
     * @param int $townId
     * @param int $addressId
     */
    public function handleTownDeleteAddress($townId, $addressId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Town:edit', $presenter->getParameter('id'));
        }

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

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentTownDeleteAddressForm()
    {
        $deleteModalFormSettings = new DeleteModalFormSettings();
        $deleteModalFormSettings->callBack = [$this, 'townDeleteAddressFormYesOnClick'];

        $form = $this->deleteModalForm->create($deleteModalFormSettings);

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
            $presenter->redirect('Town:edit', $values->townId);
        }

        try {
            $this->addressManager->deleteByPrimaryKey($values->addressId);

            $addresses = $this->addressFacade->getByTownIdCached($values->townId);

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
