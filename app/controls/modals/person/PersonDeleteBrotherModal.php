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
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
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
     * @var ITranslator $translator
     */
    private $translator;

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
     * @param ITranslator $translator
     * @param PersonFilter $personFilter
     * @param PersonFacade $personFacade
     * @param PersonSettingsManager $personSettingsManager
     * @param PersonManager $personManager
     * @param PersonUpdateService $personUpdateService
     */
    public function __construct(
        ITranslator $translator,
        PersonFilter $personFilter,
        PersonFacade $personFacade,
        PersonSettingsManager $personSettingsManager,
        PersonManager $personManager,
        PersonUpdateService $personUpdateService
    ) {
        parent::__construct();

        $this->translator = $translator;
        $this->personFilter = $personFilter;
        $this->personFacade = $personFacade;
        $this->personSettingsManager = $personSettingsManager;
        $this->personManager = $personManager;
        $this->personUpdateService = $personUpdateService;
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
        $formFactory = new DeleteModalForm($this->translator);
        $form = $formFactory->create([$this, 'personDeleteBrotherFormYesOnClick']);

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
