<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownDeletePersonDeathModal.php
 * User: Tomáš Babický
 * Date: 22.11.2020
 * Time: 19:35
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
 * Class AddressDeleteDeathPersonModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Address
 */
class AddressDeleteDeathPersonModal extends Control
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
     * AddressDeleteDeathPersonModal constructor.
     *
     * @param AddressFacade $addressFacade
     * @param PersonFacade $personFacade
     * @param AddressFilter $addressFilter
     * @param PersonFilter $personFilter
     * @param DeleteModalForm $deleteModalForm
     * @param PersonManager $personManager
     * @param PersonSettingsManager $personSettingsManager
     */
    public function __construct(
        AddressFacade $addressFacade,
        PersonFacade $personFacade,

        AddressFilter $addressFilter,
        PersonFilter $personFilter,

        DeleteModalForm $deleteModalForm,

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
        $this['addressDeleteDeathPersonForm']->render();
    }

    /**
     * @param int $addressId
     * @param int $personId
     */
    public function handleAddressDeleteDeathPerson($addressId, $personId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Address:edit', $presenter->getParameter('id'));
        }

        $this['addressDeleteDeathPersonForm']->setDefaults(
            [
                'personId' => $personId,
                'addressId' => $addressId
            ]
        );

        $personFilter = $this->personFilter;
        $addressFilter = $this->addressFilter;

        $addressModalItem = $this->addressFacade->getByPrimaryKeyCached($addressId);
        $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);

        $presenter->template->modalName = 'addressDeleteDeathPerson';
        $presenter->template->addressModalItem = $addressFilter($addressModalItem);
        $presenter->template->personModalItem = $personFilter($personModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentAddressDeleteDeathPersonForm()
    {
        $deleteModalFormSettings = new DeleteModalFormSettings();
        $deleteModalFormSettings->callBack = [$this, 'addressDeleteDeathPersonFormYesOnClick'];

        $form = $this->deleteModalForm->create($deleteModalFormSettings);

        $form->addHidden('personId');
        $form->addHidden('addressId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function addressDeleteDeathPersonFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Address:edit', $presenter->getParameter('id'));
        }

        $this->personManager->updateByPrimaryKey($values->personId, ['deathAddressId' => null]);

        $deathPersons = $this->personSettingsManager->getByDeathAddressId($values->personId);

        $presenter->template->deathPersons = $deathPersons;

        $presenter->payload->showModal = false;

        $presenter->flashMessage('person_saved', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('death_persons');
    }
}
