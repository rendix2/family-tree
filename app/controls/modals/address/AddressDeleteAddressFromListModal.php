<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteAddressFromListModal.php
 * User: Tomáš Babický
 * Date: 16.11.2020
 * Time: 21:16
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Address;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Localization\ITranslator;
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
 * Class AddressDeleteAddressFromListModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Address
 */
class AddressDeleteAddressFromListModal extends Control
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
     * AddressDeleteAddressFromListModal constructor.
     *
     * @param AddressFacade   $addressFacade
     * @param AddressFilter   $addressFilter
     * @param AddressManager  $addressManager
     * @param DeleteModalForm $deleteModalForm
     */
    public function __construct(
        AddressFacade $addressFacade,
        AddressFilter $addressFilter,
        AddressManager $addressManager,
        DeleteModalForm $deleteModalForm
    ) {
        parent::__construct();

        $this->addressFacade = $addressFacade;
        $this->addressFilter = $addressFilter;
        $this->deleteModalForm = $deleteModalForm;
        $this->addressManager = $addressManager;
    }

    /**
     * @param int $addressId
     */
    public function handleAddressDeleteAddressFromList($addressId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Address:default');
        }

        $addressModalItem = $this->addressFacade->getByPrimaryKeyCached($addressId);

        $this['addressDeleteListFromListForm']->setDefaults(['addressId' => $addressId]);

        $addressFiler = $this->addressFilter;

        $presenter->template->modalName = 'addressDeleteAddressFromList';
        $presenter->template->addressModalItem = $addressFiler($addressModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    public function render()
    {
        $this['addressDeleteListFromListForm']->render();
    }

    /**
     * @return Form
     */
    protected function createComponentAddressDeleteListFromListForm()
    {
        $deleteModalFormSettings = new DeleteModalFormSettings();
        $deleteModalFormSettings->callBack = [$this, 'addressDeleteListFromListFormYesOnClick'];

        $form = $this->deleteModalForm->create($deleteModalFormSettings);

        $form->addHidden('addressId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash    $values
     */
    public function addressDeleteListFromListFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Address:default');
        }

        try {
            $this->addressManager->deleteByPrimaryKey($values->addressId);

            $presenter->flashMessage('address_deleted', BasePresenter::FLASH_SUCCESS);

            $presenter->redrawControl('list');
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
