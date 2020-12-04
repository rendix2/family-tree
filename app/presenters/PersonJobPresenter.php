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
use Rendix2\FamilyTree\App\Forms\Person2JobForm;
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
     * @param JobFacade $jobFacade
     * @param JobManager $addressManager
     * @param Person2JobManager $person2JobManager
     * @param Person2JobFacade $person2JobFacade
     * @param PersonFacade $personFacade
     * @param PersonManager $personManager
     */
    public function __construct(
        JobFacade $jobFacade,
        JobManager $addressManager,
        Person2JobManager $person2JobManager,
        Person2JobFacade $person2JobFacade,
        PersonFacade $personFacade,
        PersonManager $personManager
    ) {
        parent::__construct();

        $this->jobFacade = $jobFacade;
        $this->jobManager = $addressManager;
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

            $this['personJobForm-personId']->setDefaultValue($relation->person->id);
            $this['personJobForm-jobId']->setDefaultValue($relation->job->id);

            $this['personJobForm-dateSince']->setDefaultValue($relation->duration->dateSince);
            $this['personJobForm-dateTo']->setDefaultValue($relation->duration->dateTo);
            $this['personJobForm-untilNow']->setDefaultValue($relation->duration->untilNow);

            $this['personJobForm']->setDefaults((array)$relation);
        } elseif ($personId && !$jobId) {
            $person = $this->personManager->getByPrimaryKey($personId);

            if (!$person) {
                $this->error('Item not found.');
            }

            $this['personJobForm-personId']->setDefaultValue($personId);
        } elseif (!$personId && $jobId) {
            $job = $this->jobManager->getByPrimaryKey($jobId);

            if (!$job) {
                $this->error('Item not found.');
            }

            $this['personJobForm-jobId']->setDefaultValue($jobId);
        }
    }

    /**
     * @return Form
     */
    protected function createComponentPersonJobForm()
    {
        $formFactory = new Person2JobForm($this->getTranslator());

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
            $this->person2JobManager->updateGeneral($personId, $jobId, (array)$values);

            $this->flashMessage('person_job_saved', self::FLASH_SUCCESS);

            $this->redirect('PersonJob:edit', $personId, $jobId);
        } else {
            $this->person2JobManager->addGeneral((array) $values);

            $this->flashMessage('person_job_added', self::FLASH_SUCCESS);

            $this->redirect('PersonJob:edit', $values->personId, $values->jobId);
        }
    }
}
