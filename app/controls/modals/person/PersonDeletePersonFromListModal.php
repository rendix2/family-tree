<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeletePersonFromListModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 1:44
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

/**
 * Class PersonDeletePersonFromListModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonDeletePersonFromListModal extends Control
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
        $this['personDeletePersonFromListForm']->render();
    }

    /**
     * @param int $personId
     */
    public function handlePersonDeletePersonFromList($personId)
    {
        if (!$this->presenter->isAjax()) {
            $this->presenter->redirect('Person:edit', $this->presenter->getParameter('id'));
        }

        if ($this->presenter->isAjax()) {
            $this['personDeletePersonFromListForm']->setDefaults(['personId' => $personId]);

            $personFilter = $this->personFilter;

            $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);

            $this->presenter->template->modalName = 'personDeletePersonFromList';
            $this->presenter->template->personModalItem = $personFilter($personModalItem);

            $this->presenter->payload->showModal = true;

            $this->presenter->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentPersonDeletePersonFromListForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'personDeletePersonFromListFormYesOnClick']);
        $form->addHidden('personId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function personDeletePersonFromListFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        try {
            $this->personManager->deleteByPrimaryKey($values->personId);

            $this->presenter->flashMessage('person_deleted', BasePresenter::FLASH_SUCCESS);

            $this->presenter->redrawControl('flashes');
            $this->presenter->redrawControl('list');
        } catch (ForeignKeyConstraintViolationException $e) {
            if ($e->getCode() === 1451) {
                $this->presenter->flashMessage('Item has some unset relations', BasePresenter::FLASH_DANGER);
                $this->presenter->redrawControl('flashes');
            } else {
                Debugger::log($e, ILogger::EXCEPTION);
            }
        }
    }
}
