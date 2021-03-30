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
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Facades\Person2AddressFacade;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;

use Rendix2\FamilyTree\App\Managers\Person2AddressManager;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class PersonDeletePersonAddressModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonDeletePersonAddressModal extends Control
{
    /**
     * @var ITranslator $translator
     */
    private $translator;

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
     * @param ITranslator $translator
     * @param Person2AddressFacade $person2AddressFacade
     * @param Person2AddressManager $person2AddressManager
     * @param AddressFacade $addressFacade
     * @param PersonFacade $personFacade
     * @param PersonFilter $personFilter
     * @param AddressFilter $addressFilter
     */
    public function __construct(
        Person2AddressFacade $person2AddressFacade,

        DeleteModalForm $deleteModalForm,

        Person2AddressManager $person2AddressManager,
        AddressFacade $addressFacade,
        PersonFacade $personFacade,
        PersonFilter $personFilter,
        AddressFilter $addressFilter
    ) {
        parent::__construct();

        $this->person2AddressFacade = $person2AddressFacade;
        $this->person2AddressManager = $person2AddressManager;
        $this->addressFacade = $addressFacade;
        $this->personFacade = $personFacade;
        $this->personFilter = $personFilter;
        $this->addressFilter = $addressFilter;
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

        $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);
        $addressModalItem = $this->addressFacade->getByPrimaryKeyCached($addressId);

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
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'personDeletePersonAddressFormYesOnClick']);
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

        $this->person2AddressManager->deleteByLeftIdAndRightId($values->personId, $values->addressId);

        $addresses = $this->person2AddressFacade->getByLeft($values->personId);

        $presenter->template->addresses = $addresses;

        $presenter->payload->showModal = false;

        $presenter->flashMessage('person_address_deleted', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('addresses');
    }
}
