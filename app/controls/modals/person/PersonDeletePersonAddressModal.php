<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeletePersonAddressModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:13
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\DeleteModalFormSettings;
use Rendix2\FamilyTree\App\Model\Facades\Person2AddressFacade;
use Rendix2\FamilyTree\App\Model\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Model\Managers\Person2AddressManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class PersonDeletePersonAddressModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonDeletePersonAddressModal extends Control
{
    /**
     * @var DeleteModalForm $deleteModalForm
     */
    private $deleteModalForm;

    /**
     * @var Person2AddressFacade $person2AddressFacade
     */
    private $person2AddressFacade;

    /**
     * @var Person2AddressManager $person2AddressManager
     */
    private $person2AddressManager;

    /**
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var PersonFilter $personFilter
     */
    private $personFilter;

    /**
     * @var AddressFilter $addressFilter
     */
    private $addressFilter;

    /**
     * PersonDeletePersonAddressModal constructor.
     *
     * @param Person2AddressFacade  $person2AddressFacade
     * @param DeleteModalForm       $deleteModalForm
     * @param Person2AddressManager $person2AddressManager
     * @param AddressFacade         $addressFacade
     * @param PersonFacade          $personFacade
     * @param PersonFilter          $personFilterCached
     * @param AddressFilter         $addressFilter
     */
    public function __construct(
        Person2AddressFacade $person2AddressFacade,

        DeleteModalForm $deleteModalForm,

        Person2AddressManager $person2AddressManager,
        AddressFacade $addressFacade,
        PersonFacade $personFacade,
        PersonFilter $personFilterCached,
        AddressFilter $addressFilter
    ) {
        parent::__construct();

        $this->person2AddressFacade = $person2AddressFacade;
        $this->person2AddressManager = $person2AddressManager;
        $this->addressFacade = $addressFacade;
        $this->personFacade = $personFacade;
        $this->personFilter = $personFilterCached;
        $this->addressFilter = $addressFilter;

        $this->deleteModalForm = $deleteModalForm;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personDeletePersonAddressForm']->render();
    }

    /**
     * @param int $personId
     * @param int $addressId
     */
    public function handlePersonDeletePersonAddress($personId, $addressId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $this['personDeletePersonAddressForm']->setDefaults(
            [
                'personId' => $personId,
                'addressId' => $addressId
            ]
        );

        $addressFilter = $this->addressFilter;
        $personFilter = $this->personFilter;

        $personModalItem = $this->personFacade->select()->getCachedManager()->getByPrimaryKey($personId);
        $addressModalItem = $this->addressFacade->select()->getCachedManager()->getByPrimaryKey($addressId);

        $presenter->template->modalName = 'personDeletePersonAddress';
        $presenter->template->addressModalItem = $addressFilter($addressModalItem);
        $presenter->template->personModalItem = $personFilter($personModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonDeletePersonAddressForm()
    {
        $deleteModalFormSettings = new DeleteModalFormSettings();
        $deleteModalFormSettings->callBack = [$this, 'personDeletePersonAddressFormYesOnClick'];

        $form = $this->deleteModalForm->create($deleteModalFormSettings);

        $form->addHidden('personId');
        $form->addHidden('addressId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function personDeletePersonAddressFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $this->person2AddressManager->delete()->deleteByLeftAndRightKey($values->personId, $values->addressId);

        $addresses = $this->person2AddressFacade->select()->getCachedManager()->getByLeftKey($values->personId);

        $presenter->template->addresses = $addresses;

        $presenter->payload->showModal = false;

        $presenter->flashMessage('person_address_deleted', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('addresses');
    }
}
