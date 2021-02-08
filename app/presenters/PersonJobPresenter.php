<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonJobPresenter.php
 * User: Tomáš Babický
 * Date: 28.10.2020
 * Time: 17:14
 */

namespace Rendix2\FamilyTree\App\Presenters;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Facades\Person2JobFacade;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Filters\DurationFilter;
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\FormJsonDataParser;
use Rendix2\FamilyTree\App\Forms\Person2JobForm;
use Rendix2\FamilyTree\App\Forms\Settings\PersonJobSettings;
use Rendix2\FamilyTree\App\Managers\JobManager;
use Rendix2\FamilyTree\App\Managers\Person2JobManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Model\Facades\JobFacade;
use Rendix2\FamilyTree\App\Presenters\Traits\PersonJob\PersonJobDeletePersonJobFromEditModal;
use Rendix2\FamilyTree\App\Presenters\Traits\PersonJob\PersonJobDeletePersonJobFromListModal;

/**
 * Class PersonJobPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class PersonJobPresenter extends BasePresenter
{
    use PersonJobDeletePersonJobFromListModal;
    use PersonJobDeletePersonJobFromEditModal;

    /**
     * @var JobFacade $jobFacade
     */
    private $jobFacade;

    /**
     * @var JobManager
     */
    private $jobManager;

    /**
     * @var Person2JobFacade $person2JobFacade
     */
    private $person2JobFacade;

    /**
     * @var Person2JobManager $person2JobManager
     */
    private $person2JobManager;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var PersonManager
     */
    private $personManager;

    /**
     * PersonJobPresenter constructor.
     *
     * @param JobFacade $jobFacade
     * @param JobManager $jobManager
     * @param Person2JobManager $person2JobManager
     * @param Person2JobFacade $person2JobFacade
     * @param PersonFacade $personFacade
     * @param PersonManager $personManager
     */
    public function __construct(
        JobFacade $jobFacade,
        JobManager $jobManager,
        Person2JobManager $person2JobManager,
        Person2JobFacade $person2JobFacade,
        PersonFacade $personFacade,
        PersonManager $personManager
    ) {
        parent::__construct();

        $this->jobFacade = $jobFacade;
        $this->jobManager = $jobManager;

        $this->person2JobFacade = $person2JobFacade;
        $this->person2JobManager = $person2JobManager;

        $this->personFacade = $personFacade;
        $this->personManager = $personManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $relations = $this->person2JobFacade->getAllCached();

        $this->template->relations = $relations;

        $this->template->addFilter('job', new JobFilter());
        $this->template->addFilter('person', new PersonFilter($this->getTranslator(), $this->getHttpRequest()));
        $this->template->addFilter('duration', new DurationFilter($this->getTranslator()));
    }

    /**
     * @param int $_jobId
     * @param string $formData
     */
    public function handlePersonJobFormSelectJob($_jobId, $formData)
    {
        if (!$this->isAjax()) {
            $this->redirect('PersonJob:edit', null, $_jobId);
        }

        $formDataParsed = FormJsonDataParser::parse($formData);
        unset($formDataParsed['jobId']);

        if ($_jobId) {
            $selectedPersons = $this->person2JobManager->getPairsByRight($_jobId);

            foreach ($selectedPersons as $key => $selectedPerson) {
                if ($selectedPerson === $this['personJobForm-personId']->getValue()) {
                    unset($selectedPersons[$key]);

                    break;
                }
            }

            $this['personJobForm-jobId']->setDefaultValue($_jobId);
            $this['personJobForm-personId']->setDisabled($selectedPersons);
        } else {
            $persons = $this->personManager->getAllPairsCached($this->getTranslator());
            $jobs = $this->jobFacade->getPairsCached();

            $this['personJobForm-personId']->setItems($persons);
            $this['personJobForm-jobId']->setItems($jobs);
        }

        $this['personJobForm']->setDefaults((array) $formDataParsed);

        $this->payload->snippets = [
            $this['personJobForm-personId']->getHtmlId() => (string) $this['personJobForm-personId']->getControl(),
        ];

        $this->redrawControl('jsFormCallback');
    }

    /**
     * @param int $_personId
     * @param string $formData
     */
    public function handlePersonJobFormSelectPerson($_personId, $formData)
    {
        if (!$this->isAjax()) {
            $this->redirect('PersonJob:edit', $_personId);
        }

        $formDataParsed = FormJsonDataParser::parse($formData);
        unset($formDataParsed['personId']);

        if ($_personId) {
            $selectedJobs = $this->person2JobManager->getPairsByLeft($_personId);

            foreach ($selectedJobs as $key => $selectedJob) {
                if ($selectedJob === $this['personJobForm-jobId']->getValue()) {
                    unset($selectedJobs[$key]);

                    break;
                }
            }

            $this['personJobForm-personId']->setDefaultValue($_personId);
            $this['personJobForm-jobId']->setDisabled($selectedJobs);
        } else {
            $persons = $this->personManager->getAllPairsCached($this->getTranslator());
            $jobs = $this->jobFacade->getPairsCached();

            $this['personJobForm-personId']->setItems($persons);
            $this['personJobForm-jobId']->setItems($jobs);
        }

        $this['personJobForm']->setDefaults((array) $formDataParsed);

        $this->payload->snippets = [
            $this['personJobForm-jobId']->getHtmlId() => (string) $this['personJobForm-jobId']->getControl(),
        ];

        $this->redrawControl('jsFormCallback');
    }

    /**
     * @param int $personId
     * @param int $jobId
     */
    public function actionEdit($personId, $jobId)
    {
        $persons = $this->personManager->getAllPairsCached($this->getTranslator());
        $jobs = $this->jobManager->getAllPairsCached();

        $this['personJobForm-personId']->setItems($persons);
        $this['personJobForm-jobId']->setItems($jobs);

        if ($personId && $jobId) {
            $relation = $this->person2JobFacade->getByLeftAndRightCached($personId, $jobId);

            if (!$relation) {
                $this->error('Item not found.');
            }

            $selectedPersons = $this->person2JobManager->getPairsByRight($jobId);
            $selectedJobs = $this->person2JobManager->getPairsByLeft($personId);

            foreach ($selectedJobs as $key => $selectedJob) {
                if ($selectedJob === $relation->job->id) {
                    unset($selectedJobs[$key]);

                    break;
                }
            }

            foreach ($selectedPersons as $key => $selectedPerson) {
                if ($selectedPerson === $relation->person->id) {
                    unset($selectedPersons[$key]);

                    break;
                }
            }

            $this['personJobForm-personId']->setDisabled($selectedPersons)
                ->setDefaultValue($relation->person->id);

            $this['personJobForm-jobId']->setDisabled($selectedJobs)
                ->setDefaultValue($relation->job->id);

            $this['personJobForm-dateSince']->setDefaultValue($relation->duration->dateSince);
            $this['personJobForm-dateTo']->setDefaultValue($relation->duration->dateTo);
            $this['personJobForm-untilNow']->setDefaultValue($relation->duration->untilNow);

            $this['personJobForm']->setDefaults((array) $relation);
        } elseif ($personId && !$jobId) {
            $person = $this->personManager->getByPrimaryKey($personId);

            if (!$person) {
                $this->error('Item not found.');
            }

            $selectedJobs = $this->person2JobManager->getPairsByLeft($personId);

            foreach ($selectedJobs as $key => $selectedJob) {
                if ($selectedJob === $this['personJobForm-jobId']->getValue()) {
                    unset($selectedJobs[$key]);

                    break;
                }
            }

            $this['personJobForm-personId']->setDefaultValue($personId);
            $this['personJobForm-jobId']->setDisabled($selectedJobs);
        } elseif (!$personId && $jobId) {
            $job = $this->jobManager->getByPrimaryKey($jobId);

            if (!$job) {
                $this->error('Item not found.');
            }

            $selectedPersons = $this->person2JobManager->getPairsByRight($jobId);

            foreach ($selectedPersons as $key => $selectedPerson) {
                if ($selectedPerson === $this['personJobForm-personId']->getValue()) {
                    unset($selectedPersons[$key]);

                    break;
                }
            }

            $this['personJobForm-jobId']->setDefaultValue($jobId);
            $this['personJobForm-personId']->setDisabled($selectedPersons);
        }
    }

    /**
     * @return Form
     */
    protected function createComponentPersonJobForm()
    {
        $personJobSettings = new PersonJobSettings();
        $personJobSettings->selectJobHandle = $this->link('personJobFormSelectJob!');
        $personJobSettings->selectPersonHandle = $this->link('personJobFormSelectPerson!');

        $formFactory = new Person2JobForm($this->getTranslator(), $personJobSettings);

        $form = $formFactory->create();
        $form->onSuccess[] = [$this, 'personJobSuccess'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personJobSuccess(Form $form, ArrayHash $values)
    {
        $personId = $this->getParameter('personId');
        $jobId = $this->getParameter('jobId');

        if ($personId !== null && $jobId !== null) {
            $this->person2JobManager->updateGeneral($personId, $jobId, (array) $values);

            $this->flashMessage('person_job_saved', self::FLASH_SUCCESS);

            $this->redirect('PersonJob:edit', $values->personId, $values->jobId);
        } else {
            $this->person2JobManager->addGeneral((array) $values);

            $this->flashMessage('person_job_added', self::FLASH_SUCCESS);

            $this->redirect('PersonJob:edit', $values->personId, $values->jobId);
        }
    }
}
