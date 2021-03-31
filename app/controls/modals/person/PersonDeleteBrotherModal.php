<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteBrotherModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:08
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\DeleteModalFormSettings;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Filters\PersonFilter;

use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Rendix2\FamilyTree\App\Services\PersonUpdateService;

/**
 * Class PersonDeleteBrotherModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonDeleteBrotherModal extends Control
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
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var PersonSettingsManager $personSettingsManager
     */
    private $personSettingsManager;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var PersonUpdateService $personUpdateService
     */
    private $personUpdateService;

    /**
     * PersonDeleteBrotherModal constructor.
     *
     * @param PersonFilter          $personFilter
     * @param PersonFacade          $personFacade
     * @param DeleteModalForm       $deleteModalForm
     * @param PersonSettingsManager $personSettingsManager
     * @param PersonManager         $personManager
     * @param PersonUpdateService   $personUpdateService
     */
    public function __construct(
        PersonFilter $personFilter,
        PersonFacade $personFacade,

        DeleteModalForm $deleteModalForm,

        PersonSettingsManager $personSettingsManager,
        PersonManager $personManager,
        PersonUpdateService $personUpdateService
    ) {
        parent::__construct();

        $this->personFilter = $personFilter;
        $this->personFacade = $personFacade;
        $this->personSettingsManager = $personSettingsManager;
        $this->personManager = $personManager;
        $this->personUpdateService = $personUpdateService;

        $this->deleteModalForm = $deleteModalForm;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personDeleteBrotherForm']->render();
    }

    /**
     * @param int $personId
     * @param int $brotherId
     */
    public function handlePersonDeleteBrother($personId, $brotherId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $this['personDeleteBrotherForm']->setDefaults(
            [
                'personId' => $personId,
                'brotherId' => $brotherId
            ]
        );

        $personFilter = $this->personFilter;

        $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);
        $brotherModalItem = $this->personSettingsManager->getByPrimaryKeyCached($brotherId);

        $presenter->template->modalName = 'personDeleteBrother';
        $presenter->template->brotherModalItem = $personFilter($brotherModalItem);
        $presenter->template->personModalItem = $personFilter($personModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonDeleteBrotherForm()
    {
        $deleteModalFormSettings = new DeleteModalFormSettings();
        $deleteModalFormSettings->callBack = [$this, 'personDeleteBrotherFormYesOnClick'];

        $form = $this->deleteModalForm->create($deleteModalFormSettings);

        $form->addHidden('personId');
        $form->addHidden('brotherId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function personDeleteBrotherFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $this->personManager->updateByPrimaryKey($values->brotherId,
            [
                'fatherId' => null,
                'motherId' => null
            ]
        );

        $brother = $this->personFacade->getByPrimaryKeyCached($values->brotherId);

        $this->personUpdateService->prepareBrothersAndSisters(
            $presenter,
            $values->brotherId,
            $brother->father,
            $brother->mother
        );

        $presenter->payload->showModal = false;

        $presenter->flashMessage('person_brother_deleted', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('brothers');
    }
}
