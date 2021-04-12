<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownDeletePersonGravedModal.php
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
use Rendix2\FamilyTree\App\Model\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Model\Managers\PersonManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class AddressDeleteGravedPersonModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Address
 */
class AddressDeleteGravedPersonModal extends Control
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
     * AddressDeleteGravedPersonModal constructor.
     *
     * @param AddressFacade   $addressFacade
     * @param PersonFacade    $personFacade
     * @param AddressFilter   $addressFilter
     * @param PersonFilter    $personFilter
     * @param DeleteModalForm $deleteModalForm
     * @param PersonManager   $personManager
     */
    public function __construct(
        AddressFacade $addressFacade,
        PersonFacade $personFacade,

        AddressFilter $addressFilter,
        PersonFilter $personFilter,

        DeleteModalForm $deleteModalForm,

        PersonManager $personManager
    ) {
        parent::__construct();

        $this->addressFacade = $addressFacade;
        $this->personFacade = $personFacade;

        $this->addressFilter = $addressFilter;
        $this->personFilter = $personFilter;

        $this->deleteModalForm = $deleteModalForm;

        $this->personManager = $personManager;
    }

    public function render()
    {
        $this['addressDeleteGravedPersonForm']->render();
    }

    /**
     * @param int $addressId
     * @param int $personId
     */
    public function handleAddressDeleteGravedPerson($addressId, $personId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Address:edit', $presenter->getParameter('id'));
        }

        $this['addressDeleteGravedPersonForm']->setDefaults(
            [
                'personId' => $personId,
                'addressId' => $addressId,
            ]
        );

        $personFilter = $this->personFilter;
        $addressFilter = $this->addressFilter;

        $addressModalItem = $this->addressFacade->select()->getCachedManager()->getByPrimaryKey($addressId);
        $personModalItem = $this->personFacade->select()->getCachedManager()->getByPrimaryKey($personId);

        $presenter->template->modalName = 'addressDeleteGravedPerson';
        $presenter->template->addressModalItem = $addressFilter($addressModalItem);
        $presenter->template->personModalItem = $personFilter($personModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentAddressDeleteGravedPersonForm()
    {
        $deleteModalFormSettings = new DeleteModalFormSettings();
        $deleteModalFormSettings->callBack = [$this, 'addressDeleteGravedPersonFormYesOnClick'];

        $form = $this->deleteModalForm->create($deleteModalFormSettings);

        $form->addHidden('personId');
        $form->addHidden('addressId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash    $values
     */
    public function addressDeleteGravedPersonFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Address:edit', $presenter->getParameter('id'));
        }

        $this->personManager->update()->updateByPrimaryKey($values->personId, ['gravedAddressId' => null]);

        $gravedPersons = $this->personManager->select()->getSettingsCachedManager()->getByGravedAddressId($values->personId);

        $presenter->template->gravedPersons = $gravedPersons;

        $presenter->payload->showModal = false;

        $presenter->flashMessage('person_saved', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('graved_persons');
    }
}
