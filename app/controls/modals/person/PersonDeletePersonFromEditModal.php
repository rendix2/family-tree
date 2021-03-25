<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeletePersonFromEditModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 1:15
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Tracy\Debugger;
use Tracy\ILogger;


class PersonDeletePersonFromEditModal extends Control
{
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
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * PersonDeletePersonFromEditModal constructor.
     *
     * @param ITranslator $translator
     * @param PersonFacade $personFacade
     * @param PersonFilter $personFilter
     * @param PersonManager $personManager
     */
    public function __construct(
        ITranslator $translator,
        PersonFacade $personFacade,
        PersonFilter $personFilter,
        PersonManager $personManager
    ) {
        parent::__construct();

        $this->personFacade = $personFacade;
        $this->personFilter = $personFilter;
        $this->personManager = $personManager;
        $this->translator = $translator;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personDeletePersonFromEditForm']->render();
    }

    /**
     * @param int $personId
     * @param int $deletePersonId
     */
    public function handlePersonDeletePersonFromEdit($personId, $deletePersonId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        if ($presenter->isAjax()) {
            $this['personDeletePersonFromEditForm']->setDefaults(
                [
                    'deletePersonId' => $deletePersonId,
                    'personId' => $personId
                ]
            );

            $personFilter = $this->personFilter;

            $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);

            $presenter->template->modalName = 'personDeletePersonFromEdit';
            $presenter->template->personModalItem = $personFilter($personModalItem);

            $presenter->payload->showModal = true;

            $presenter->presenter->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentPersonDeletePersonFromEditForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'personDeletePersonFromEditFormYesOnClick'], true);
        $form->addHidden('deletePersonId');
        $form->addHidden('personId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function personDeletePersonFromEditFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        try {
            $this->personManager->deleteByPrimaryKey($values->personId);

            $presenter->flashMessage('person_deleted', BasePresenter::FLASH_SUCCESS);
        } catch (ForeignKeyConstraintViolationException $e) {
            if ($e->getCode() === 1451) {
                $presenter->flashMessage('Item has some unset relations', BasePresenter::FLASH_DANGER);
            } else {
                Debugger::log($e, ILogger::EXCEPTION);
            }
        }

        if ($values->personId === $values->deletePersonId) {
            $presenter->redirect('Person:default');
        } else {
            $presenter->redrawControl();
        }
    }
}