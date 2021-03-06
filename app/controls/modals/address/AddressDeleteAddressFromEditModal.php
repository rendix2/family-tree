<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteAddressEditModal.php
 * User: Tomáš Babický
 * Date: 16.11.2020
 * Time: 21:12
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Address;

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
 * Class AddressDeleteAddressFromEditModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Address
 */
class AddressDeleteAddressFromEditModal extends Control
{
    /**
     * @var AddressManager $addressManager
     */
    private $addressManager;

    /**
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * @var DeleteModalForm $deleteModalForm
     */
    private $deleteModalForm;

    /**
     * @var AddressFilter $addressFilter
     */
    private $addressFilter;

    /**
     * AddressDeleteAddressFromEditModal constructor.
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

        $this->addressFacade = $addressFacade;
        $this->addressFilter = $addressFilter;
        $this->deleteModalForm = $deleteModalForm;
        $this->addressManager = $addressManager;
    }

    public function render()
    {
        $this['addressDeleteAddressFromEditForm']->render();
    }

    /**
     * @param int $addressId
     */
    public function handleAddressDeleteAddressFromEdit($addressId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Address:edit', $presenter->getParameter('id'));
        }

        $this['addressDeleteAddressFromEditForm']->setDefaults(['addressId' => $addressId]);

        $addressFilter = $this->addressFilter;

        $addressModalItem = $this->addressFacade->select()->getCachedManager()->getByPrimaryKey($addressId);

        $presenter->template->modalName = 'addressDeleteAddressFromEdit';
        $presenter->template->addressModalItem = $addressFilter($addressModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentAddressDeleteAddressFromEditForm()
    {
        $deleteModalFormSettings = new DeleteModalFormSettings();
        $deleteModalFormSettings->callBack = [$this, 'addressDeleteListFromEditFormYesOnClick'];
        $deleteModalFormSettings->httpRedirect = true;

        $form = $this->deleteModalForm->create($deleteModalFormSettings);
        $form->addHidden('addressId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash    $values
     */
    public function addressDeleteListFromEditFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Address:edit', $presenter->getParameter('id'));
        }

        try {
            $this->addressManager->delete()->deleteByPrimaryKey($values->addressId);

            $presenter->flashMessage('address_deleted', BasePresenter::FLASH_SUCCESS);
        } catch (ForeignKeyConstraintViolationException $e) {
            if ($e->getCode() === 1451) {
                $presenter->flashMessage('Item has some unset relations', BasePresenter::FLASH_DANGER);
            } else {
                Debugger::log($e, ILogger::EXCEPTION);
            }
        }

        $presenter->redirect('Address:default');
    }
}
