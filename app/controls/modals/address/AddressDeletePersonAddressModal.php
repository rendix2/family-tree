<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressPersonDeleteModal.php
 * User: Tomáš Babický
 * Date: 28.10.2020
 * Time: 20:26
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Address;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Facades\Person2AddressFacade;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Managers\Person2AddressManager;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class AddressDeletePersonAddressModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Address
 */
class AddressDeletePersonAddressModal extends Control
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
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var PersonFilter $personFilter
     */
    private $personFilter;

    /**
     * @var Person2AddressFacade $person2AddressFacade
     */
    private $person2AddressFacade;

    /**
     * @var Person2AddressManager $person2AddressManager
     */
    private $person2AddressManager;

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * AddressDeletePersonAddressModal constructor.
     * @param AddressFacade $addressFacade
     * @param AddressFilter $addressFilter
     * @param PersonFacade $personFacade
     * @param PersonFilter $personFilter
     * @param Person2AddressFacade $person2AddressFacade
     * @param Person2AddressManager $person2AddressManager
     * @param ITranslator $translator
     */
    public function __construct(
        AddressFacade $addressFacade,
        AddressFilter $addressFilter,
        PersonFacade $personFacade,
        PersonFilter $personFilter,
        Person2AddressFacade $person2AddressFacade,
        Person2AddressManager $person2AddressManager,
        ITranslator $translator
    ) {
        parent::__construct();

        $this->addressFacade = $addressFacade;
        $this->addressFilter = $addressFilter;
        $this->personFacade = $personFacade;
        $this->personFilter = $personFilter;
        $this->person2AddressFacade = $person2AddressFacade;
        $this->person2AddressManager = $person2AddressManager;
        $this->translator = $translator;
    }

    public function render()
    {
        $this['addressDeleteAddressPersonForm']->render();
    }

    /**
     * @param int $addressId
     * @param int $personId
     */
    public function handleAddressDeleteAddressPerson($personId, $addressId)
    {
        $presenter = $this->presenter;

        if ($presenter->isAjax()) {
            $this['addressDeleteAddressPersonForm']->setDefaults(
                [
                    'addressId' => $addressId,
                    'personId' => $personId
                ]
            );

            $personFilter = $this->personFilter;
            $addressFilter = $this->addressFilter;

            $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);
            $addressModalItem = $this->addressFacade->getByPrimaryKeyCached($addressId);

            $presenter->template->modalName = 'addressDeleteAddressPerson';
            $presenter->template->addressModalItem = $addressFilter($addressModalItem);
            $presenter->template->personModalItem = $personFilter($personModalItem);

            $presenter->payload->showModal = true;

            $presenter->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentAddressDeleteAddressPersonForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'addressDeleteAddressPersonFormYesOnClick']);
        $form->addHidden('personId');
        $form->addHidden('addressId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function addressDeleteAddressPersonFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if ($presenter->isAjax()) {
            $this->person2AddressManager->deleteByLeftIdAndRightId($values->personId, $values->addressId);

            $persons = $this->person2AddressFacade->getByRight($values->addressId);

            $presenter->template->persons = $persons;

            $presenter->payload->showModal = false;

            $presenter->flashMessage('person_address_deleted', BasePresenter::FLASH_SUCCESS);

            $presenter->redrawControl('flashes');
            $presenter->redrawControl('address_persons');
        } else {
            $presenter->redirect('Address:edit', $values->addressId);
        }
    }
}
