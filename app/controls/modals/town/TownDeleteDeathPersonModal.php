<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownDeletePersonDeathModal.php
 * User: Tomáš Babický
 * Date: 22.11.2020
 * Time: 19:35
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
 * Class TownDeleteDeathPersonModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Town
 */
class TownDeleteDeathPersonModal extends Control
{
    /**
     * @var DeleteModalForm $deleteModalForm
     */
    private $deleteModalForm;

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
     * TownDeletePersonDeathModal constructor.
     *
     * @param PersonFilter $personFilter
     * @param PersonSettingsManager $personSettingsManager
     * @param PersonManager $personManager
     * @param TownFilter $townFilter
     * @param PersonFacade $personFacade
     * @param TownFacade $townFacade
     * @param ITranslator $translator
     */
    public function __construct(
        PersonFilter $personFilter,


        DeleteModalForm $deleteModalForm,

        PersonSettingsManager $personSettingsManager,
        PersonManager $personManager,
        TownFilter $townFilter,
        PersonFacade $personFacade,
        TownFacade $townFacade,
        ITranslator $translator
    ) {
        parent::__construct();

        $this->personFilter = $personFilter;
        $this->personSettingsManager = $personSettingsManager;
        $this->personManager = $personManager;
        $this->townFilter = $townFilter;
        $this->personFacade = $personFacade;
        $this->townFacade = $townFacade;
        $this->translator = $translator;
    }

    public function render()
    {
        $this['townDeleteDeathPersonForm']->render();
    }

    /**
     * @param int $townId
     * @param int $personId
     */
    public function handleTownDeleteDeathPerson($townId, $personId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Town:edit', $presenter->getParameter('id'));
        }

        $this['townDeleteDeathPersonForm']->setDefaults(
            [
                'personId' => $personId,
                'townId' => $townId
            ]
        );

        $personFilter = $this->personFilter;
        $townFilter = $this->townFilter;

        $townModalItem = $this->townFacade->getByPrimaryKeyCached($townId);
        $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);

        $presenter->template->modalName = 'townDeleteDeathPerson';
        $presenter->template->townModalItem = $townFilter($townModalItem);
        $presenter->template->personModalItem = $personFilter($personModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentTownDeleteDeathPersonForm()
    {
        $formFactory = new DeleteModalForm($this->translator);
        $form = $formFactory->create([$this, 'townDeleteDeathPersonFormYesOnClick']);

        $form->addHidden('personId');
        $form->addHidden('townId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function townDeleteDeathPersonFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Town:edit', $presenter->getParameter('id'));
        }

        $this->personManager->updateByPrimaryKey($values->personId, ['deathTownId' => null]);

        $deathPersons = $this->personSettingsManager->getByDeathTownId($values->personId);

        $presenter->template->deathPersons = $deathPersons;

        $presenter->payload->showModal = false;

        $presenter->flashMessage('person_saved', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('death_persons');
    }
}
