<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonJobDeletePersonJobFromListModalFactory.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 1:56
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\PersonJob;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\DeleteModalFormSettings;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;

use Rendix2\FamilyTree\App\Managers\Person2JobManager;
use Rendix2\FamilyTree\App\Model\Facades\JobFacade;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Class PersonJobDeletePersonJobFromListModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\PersonJob
 */
class PersonJobDeletePersonJobFromListModal extends Control
{
    /**
     * @var DeleteModalForm $deleteModalForm
     */
    private $deleteModalForm;

    /**
     * @var JobFilter $jobFilter
     */
    private $jobFilter;

    /**
     * @var JobFacade $jobFacade
     */
    private $jobFacade;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var PersonFilter $personFilter
     */
    private $personFilter;

    /**
     * @var Person2JobManager $person2JobManager
     */
    private $person2JobManager;

    /**
     * PersonJobDeletePersonJobFromListModal constructor.
     *
     * @param JobFilter $jobFilter
     * @param JobFacade $jobFacade
     * @param DeleteModalForm $deleteModalForm
     * @param PersonFacade $personFacade
     * @param PersonFilter $personFilter
     * @param Person2JobManager $person2JobManager
     */
    public function __construct(
        JobFilter $jobFilter,
        PersonFilter $personFilter,
        JobFacade $jobFacade,
        PersonFacade $personFacade,
        DeleteModalForm $deleteModalForm,
        Person2JobManager $person2JobManager
    ) {
        parent::__construct();

        $this->deleteModalForm = $deleteModalForm;

        $this->jobFacade = $jobFacade;
        $this->personFacade = $personFacade;

        $this->jobFilter = $jobFilter;
        $this->personFilter = $personFilter;

        $this->person2JobManager = $person2JobManager;
    }

    public function render()
    {
        $this['personJobDeletePersonJobFromListForm']->render();
    }

    /**
     * @param int $personId
     * @param int $jobId
     */
    public function handlePersonJobDeletePersonJobFromList($personId, $jobId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('PersonJob:default');
        }

        $this['personJobDeletePersonJobFromListForm']->setDefaults(
            [
                'personId' => $personId,
                'jobId' => $jobId
            ]
        );

        $jobFilter = $this->jobFilter;
        $personFilter = $this->personFilter;

        $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);
        $jobModalItem = $this->jobFacade->getByPrimaryKeyCached($jobId);

        $presenter->template->modalName = 'personJobDeletePersonJobFromList';
        $presenter->template->jobModalItem = $jobFilter($jobModalItem);
        $presenter->template->personModalItem = $personFilter($personModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonJobDeletePersonJobFromListForm()
    {
        $deleteModalFormSettings = new DeleteModalFormSettings();
        $deleteModalFormSettings->callBack = [$this, 'sourceDeleteSourceFromListFormYesOnClick'];

        $form = $this->deleteModalForm->create($deleteModalFormSettings);

        $form->addHidden('personId');
        $form->addHidden('jobId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function personJobDeletePersonJobFromListFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('PersonJob:default');
        }

        try {
            $this->person2JobManager->deleteByLeftIdAndRightId($values->personId, $values->jobId);

            $presenter->flashMessage('person_job_deleted', BasePresenter::FLASH_SUCCESS);

            $presenter->redrawControl('list');
        } catch (ForeignKeyConstraintViolationException $e) {
            if ($e->getCode() === 1451) {
                $presenter->flashMessage('Item has some unset relations', BasePresenter::FLASH_DANGER);
            } else {
                Debugger::log($e, ILogger::EXCEPTION);
            }
        } finally {
            $presenter->redrawControl('flashes');
        }
    }
}
