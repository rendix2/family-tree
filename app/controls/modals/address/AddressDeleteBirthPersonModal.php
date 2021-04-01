<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownDeletePersonBirthModal.php
 * User: Tomáš Babický
 * Date: 22.11.2020
 * Time: 19:34
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Address;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\DeleteModalFormSettings;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;

use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class AddressDeleteBirthPersonModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Address
 */
class AddressDeleteBirthPersonModal extends Control
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
     * @var DeleteModalForm $deleteModalForm
     */
    private $deleteModalForm;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var PersonSettingsManager $personSettingsManager
     */
    private $personSettingsManager;

    /**
     * AddressDeleteBirthPersonModal constructor.
     *
     * @param AddressFacade $addressFacade
     * @param AddressFilter $addressFilter
     * @param DeleteModalForm $deleteModalForm
     * @param PersonFacade $personFacade
     * @param PersonFilter $personFilter
     * @param PersonManager $personManager
     * @param PersonSettingsManager $personSettingsManager
     */
    public function __construct(
        AddressFacade $addressFacade,
        AddressFilter $addressFilter,

        DeleteModalForm $deleteModalForm,

        PersonFacade $personFacade,
        PersonFilter $personFilter,
        PersonManager $personManager,
        PersonSettingsManager $personSettingsManager
    ) {
        parent::__construct();

        $this->addressFacade = $addressFacade;
        $this->personFacade = $personFacade;

        $this->addressFilter = $addressFilter;
        $this->personFilter = $personFilter;

        $this->deleteModalForm = $deleteModalForm;

        $this->personManager = $personManager;

        $this->personSettingsManager = $personSettingsManager;
    }

    public function render()
    {
        $this['addressDeleteBirthPersonForm']->render();
    }

    /**
     * @param int $addressId
     * @param int $personId
     */
    public function handleAddressDeleteBirthPerson($addressId, $personId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Address:edit', $addressId);
        }

        $this['addressDeleteBirthPersonForm']->setDefaults(
            [
                'personId' => $personId,
                'addressId' => $addressId
            ]
        );

        $personFilter = $this->personFilter;
        $addressFilter = $this->addressFilter;

        $addressModalItem = $this->addressFacade->getByPrimaryKeyCached($addressId);
        $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);

        $presenter->template->modalName = 'addressDeleteBirthPerson';
        $presenter->template->addressModalItem = $addressFilter($addressModalItem);
        $presenter->template->personModalItem = $personFilter($personModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentAddressDeleteBirthPersonForm()
    {
        $deleteModalFormSettings = new DeleteModalFormSettings();
        $deleteModalFormSettings->callBack = [$this, 'addressDeleteBirthPersonFormYesOnClick'];

        $form = $this->deleteModalForm->create($deleteModalFormSettings);

        $form->addHidden('personId');
        $form->addHidden('addressId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function addressDeleteBirthPersonFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Address:edit', $presenter->getParameter('id'));
        }

        $this->personManager->updateByPrimaryKey($values->personId, ['birthAddressId' => null]);

        $birthPersons = $this->personSettingsManager->getByBirthAddressId($values->personId);

        $presenter->template->birthPersons = $birthPersons;

        $presenter->payload->showModal = false;

        $presenter->flashMessage('person_saved', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('birth_persons');
    }
}
