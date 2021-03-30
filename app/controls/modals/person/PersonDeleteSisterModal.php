<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteSisterModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:16
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Filters\PersonFilter;

use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Rendix2\FamilyTree\App\Services\PersonUpdateService;

/**
 * Class PersonDeleteSisterModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonDeleteSisterModal extends Control
{
    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var PersonFilter
     */
    private $personFilter;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var PersonUpdateService $personUpdateService
     */
    private $personUpdateService;

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * PersonDeleteSisterModal constructor.
     *
     * @param ITranslator $translator
     * @param PersonFacade $personFacade
     * @param PersonManager $personManager
     * @param PersonFilter $personFilter
     */
    public function __construct(
        PersonFacade $personFacade,
        PersonFilter $personFilter,

        DeleteModalForm $deleteModalForm,

        PersonManager $personManager,
        PersonUpdateService $personUpdateService
    ) {
        parent::__construct();

        $this->deleteModalForm = $deleteModalForm;

        $this->personFacade = $personFacade;
        $this->personFilter = $personFilter;
        $this->personManager = $personManager;
        $this->personUpdateService = $personUpdateService;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personDeleteSisterForm']->render();
    }

    /**
     * @param int $personId
     * @param int $sisterId
     */
    public function handlePersonDeleteSister($personId, $sisterId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $this['personDeleteSisterForm']->setDefaults(
            [
                'personId' => $personId,
                'sisterId' => $sisterId
            ]
        );

        $personFilter = $this->personFilter;

        $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);
        $sisterModalItem = $this->personManager->getByPrimaryKeyCached($sisterId);

        $presenter->template->modalName = 'personDeleteSister';
        $presenter->template->personModalItem = $personFilter($personModalItem);
        $presenter->template->sisterModalItem = $personFilter($sisterModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonDeleteSisterForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'personDeleteSisterFormYesOnClick']);
        $form->addHidden('personId');
        $form->addHidden('sisterId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function personDeleteSisterFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $this->personManager->updateByPrimaryKey($values->sisterId,
            [
                'fatherId' => null,
                'motherId' => null
            ]
        );

        $sister = $this->personFacade->getByPrimaryKeyCached($values->sisterId);

        $this->personUpdateService->prepareBrothersAndSisters(
            $presenter,
            $values->sisterId,
            $sister->father,
            $sister->mother
        );

        $presenter->payload->showModal = false;

        $presenter->flashMessage('person_sister_deleted', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('sisters');
        $presenter->redrawControl('flashes');
    }
}
