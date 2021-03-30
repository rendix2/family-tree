<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownDeletePersonBirthModal.php
 * User: Tomáš Babický
 * Date: 22.11.2020
 * Time: 19:34
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Town;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Filters\TownFilter;

use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;
use Rendix2\FamilyTree\App\Model\Facades\TownFacade;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class TownDeleteBirthPersonModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Town
 */
class TownDeleteBirthPersonModal extends Control
{
    /**
     * @var PersonFilter $personFilter
     */
    private $personFilter;

    /**
     * @var PersonSettingsManager $personSettingsManager
     */
    private $personSettingsManager;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var TownFilter $townFilter
     */
    private $townFilter;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var TownFacade $townFacade
     */
    private $townFacade;

    /**
     * @var DeleteModalForm $deleteModalForm
     */
    private $deleteModalForm;

    /**
     * TownDeletePersonBirthModal constructor.
     *
     * @param PersonFilter $personFilter
     * @param PersonSettingsManager $personSettingsManager
     * @param PersonManager $personManager
     * @param TownFilter $townFilter
     * @param PersonFacade $personFacade
     * @param TownFacade $townFacade
     */
    public function __construct(
        PersonFilter $personFilter,

        DeleteModalForm $deleteModalForm,

        PersonSettingsManager $personSettingsManager,
        PersonManager $personManager,
        TownFilter $townFilter,
        PersonFacade $personFacade,
        TownFacade $townFacade
    ) {
        parent::__construct();

        $this->personFilter = $personFilter;
        $this->personSettingsManager = $personSettingsManager;
        $this->personManager = $personManager;
        $this->townFilter = $townFilter;
        $this->personFacade = $personFacade;
        $this->townFacade = $townFacade;
    }

    public function render()
    {
        $this['townDeleteBirthPersonForm']->render();
    }

    /**
     * @param int $townId
     * @param int $personId
     */
    public function handleTownDeleteBirthPerson($townId, $personId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Town:edit', $presenter->getParameter('id'));
        }

        $this['townDeleteBirthPersonForm']->setDefaults(
            [
                'personId' => $personId,
                'townId' => $townId
            ]
        );

        $personFilter = $this->personFilter;
        $townFilter = $this->townFilter;

        $townModalItem = $this->townFacade->getByPrimaryKeyCached($townId);
        $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);

        $presenter->template->modalName = 'townDeleteBirthPerson';
        $presenter->template->townModalItem = $townFilter($townModalItem);
        $presenter->template->personModalItem = $personFilter($personModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentTownDeleteBirthPersonForm()
    {
        $formFactory = new DeleteModalForm($this->translator);
        $form = $formFactory->create([$this, 'townDeleteBirthPersonFormYesOnClick']);

        $form->addHidden('personId');
        $form->addHidden('townId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function townDeleteBirthPersonFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Town:edit', $presenter->getParameter('id'));
        }

        $this->personManager->updateByPrimaryKey($values->personId, ['birthTownId' => null]);

        $birthPersons = $this->personSettingsManager->getByBirthTownId($values->personId);

        $presenter->template->birthPersons = $birthPersons;

        $presenter->payload->showModal = false;

        $presenter->flashMessage('person_saved', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('birth_persons');
    }
}
