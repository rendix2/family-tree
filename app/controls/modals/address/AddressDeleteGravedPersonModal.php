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
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
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
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var PersonSettingsManager $personSettingsManager
     */
    private $personSettingsManager;

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * AddressDeleteGravedPersonModal constructor.
     *
     * @param AddressFacade $addressFacade
     * @param AddressFilter $addressFilter
     * @param PersonFacade $personFacade
     * @param PersonFilter $personFilter
     * @param PersonManager $personManager
     * @param PersonSettingsManager $personSettingsManager
     * @param ITranslator $translator
     */
    public function __construct(
        AddressFacade $addressFacade,
        AddressFilter $addressFilter,
        PersonFacade $personFacade,
        PersonFilter $personFilter,
        PersonManager $personManager,
        PersonSettingsManager $personSettingsManager,
        ITranslator $translator
    ) {
        parent::__construct();

        $this->addressFacade = $addressFacade;
        $this->addressFilter = $addressFilter;
        $this->personFacade = $personFacade;
        $this->personFilter = $personFilter;
        $this->personManager = $personManager;
        $this->personSettingsManager = $personSettingsManager;
        $this->translator = $translator;
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

        if ($presenter->isAjax()) {
            $this['addressDeleteGravedPersonForm']->setDefaults(
                [
                    'personId' => $personId,
                    'addressId' => $addressId
                ]
            );

            $personFilter = $this->personFilter;
            $addressFilter = $this->addressFilter;

            $addressModalItem = $this->addressFacade->getByPrimaryKeyCached($addressId);
            $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);

            $presenter->template->modalName = 'addressDeleteGravedPerson';
            $presenter->template->addressModalItem = $addressFilter($addressModalItem);
            $presenter->template->personModalItem = $personFilter($personModalItem);

            $presenter->payload->showModal = true;

            $presenter->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentAddressDeleteGravedPersonForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'addressDeleteGravedPersonFormYesOnClick']);
        $form->addHidden('personId');
        $form->addHidden('addressId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function addressDeleteGravedPersonFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if ($presenter->isAjax()) {
            $this->personManager->updateByPrimaryKey($values->personId, ['gravedAddressId' => null]);

            $gravedPersons = $this->personSettingsManager->getByGravedAddressId($values->personId);

            $presenter->template->gravedPersons = $gravedPersons;

            $presenter->payload->showModal = false;

            $presenter->flashMessage('person_saved', BasePresenter::FLASH_SUCCESS);

            $presenter->redrawControl('flashes');
            $presenter->redrawControl('graved_persons');
        } else {
            $presenter->redirect('Person:edit', $values->addressId);
        }
    }
}
