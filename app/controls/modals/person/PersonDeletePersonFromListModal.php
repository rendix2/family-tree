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
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\DeleteModalFormSettings;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Model\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Model\Managers\PersonManager;
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
     * @var DeleteModalForm $deleteModalForm
     */
    private $deleteModalForm;

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
     * PersonDeletePersonFromEditModal constructor.
     *
     * @param PersonFacade    $personFacade
     * @param PersonFilter    $personFilter
     * @param DeleteModalForm $deleteModalForm
     * @param PersonManager   $personManager
     */
    public function __construct(
        PersonFacade $personFacade,
        PersonFilter $personFilter,

        DeleteModalForm $deleteModalForm,

        PersonManager $personManager
    ) {
        parent::__construct();

        $this->deleteModalForm = $deleteModalForm;


        $this->personFilter = $personFilter;
        $this->personManager = $personManager;

        $this->personFacade = $personFacade;
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
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:default');
        }

        $this['personDeletePersonFromListForm']->setDefaults(['personId' => $personId]);

        $personFilter = $this->personFilter;

        $personModalItem = $this->personFacade->select()->getCachedManager()->getByPrimaryKey($personId);

        $presenter->template->modalName = 'personDeletePersonFromList';
        $presenter->template->personModalItem = $personFilter($personModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonDeletePersonFromListForm()
    {
        $deleteModalFormSettings = new DeleteModalFormSettings();
        $deleteModalFormSettings->callBack = [$this, 'personDeletePersonFromListFormYesOnClick'];

        $form = $this->deleteModalForm->create($deleteModalFormSettings);

        $form->addHidden('personId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function personDeletePersonFromListFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:default');
        }

        try {
            $this->personManager->delete()->deleteByPrimaryKey($values->personId);

            $presenter->flashMessage('person_deleted', BasePresenter::FLASH_SUCCESS);

            $presenter->redrawControl('flashes');
            $presenter->redrawControl('list');
        } catch (ForeignKeyConstraintViolationException $e) {
            if ($e->getCode() === 1451) {
                $presenter->flashMessage('Item has some unset relations', BasePresenter::FLASH_DANGER);
                $presenter->redrawControl('flashes');
            } else {
                Debugger::log($e, ILogger::EXCEPTION);
            }
        }
    }
}
