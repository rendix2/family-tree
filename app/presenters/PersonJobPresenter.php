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
use Rendix2\FamilyTree\App\Controls\Forms\Helpers\FormJsonDataParser;
use Rendix2\FamilyTree\App\Controls\Forms\Person2JobForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\PersonJobSettings;
use Rendix2\FamilyTree\App\Controls\Modals\PersonJob\Container\PersonJobModalContainer;
use Rendix2\FamilyTree\App\Controls\Modals\PersonJob\PersonJobDeletePersonJobFromEditModal;
use Rendix2\FamilyTree\App\Controls\Modals\PersonJob\PersonJobDeletePersonJobFromListModal;
use Rendix2\FamilyTree\App\Facades\Person2JobFacade;



use Rendix2\FamilyTree\App\Managers\JobManager;
use Rendix2\FamilyTree\App\Managers\JobSettingsManager;
use Rendix2\FamilyTree\App\Managers\Person2JobManager;
use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;
use Rendix2\FamilyTree\App\Model\Facades\JobSettingsFacade;

/**
 * Class PersonJobPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class PersonJobPresenter extends BasePresenter
{
    /**
     * @var JobManager
     */
    private $jobManager;

    /**
     * @var Person2JobFacade $person2JobFacade
     */
    private $person2JobFacade;

    /**
     * @var JobSettingsFacade $jobSettingsFacade
     */
    private $jobSettingsFacade;

    /**
     * @var PersonJobModalContainer $personJobModalContainer
     */
    private $personJobModalContainer;

    /**
     * @var JobSettingsManager $jobSettingsManager
     */
    private $jobSettingsManager;

    /**
     * @var Person2JobForm $person2JobForm
     */
    private $person2JobForm;

    /**
     * @var Person2JobManager $person2JobManager
     */
    private $person2JobManager;

    /**
     * @var PersonSettingsManager $personSettingsManager
     */
    private $personSettingsManager;

    /**
     * PersonJobPresenter constructor.
     *
     * @param JobSettingsFacade       $jobSettingsFacade
     * @param JobManager              $jobManager
     * @param JobSettingsManager      $jobSettingsManager
     * @param Person2JobFacade        $person2JobFacade
     * @param Person2JobForm          $person2JobForm
     * @param PersonJobModalContainer $personJobModalContainer
     * @param Person2JobManager       $person2JobManager
     * @param PersonSettingsManager   $personSettingsManager
     */
    public function __construct(
        JobSettingsFacade $jobSettingsFacade,
        JobManager $jobManager,
        JobSettingsManager $jobSettingsManager,
        Person2JobFacade $person2JobFacade,
        Person2JobForm $person2JobForm,
        PersonJobModalContainer $personJobModalContainer,
        Person2JobManager $person2JobManager,
        PersonSettingsManager $personSettingsManager
    ) {
        parent::__construct();

        $this->person2JobFacade = $person2JobFacade;
        $this->person2JobForm = $person2JobForm;

        $this->personJobModalContainer = $personJobModalContainer;

        $this->jobManager = $jobManager;
        $this->person2JobManager = $person2JobManager;

        $this->jobSettingsFacade = $jobSettingsFacade;

        $this->jobSettingsManager = $jobSettingsManager;
        $this->personSettingsManager = $personSettingsManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $relations = $this->person2JobFacade->getAllCached();

        $this->template->relations = $relations;
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
            $persons = $this->personSettingsManager->getAllPairsCached();
            $jobs = $this->jobSettingsFacade->getPairsCached();

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
            $persons = $this->personSettingsManager->getAllPairsCached();
            $jobs = $this->jobSettingsFacade->getPairsCached();

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
        $persons = $this->personSettingsManager->getAllPairsCached();
        $jobs = $this->jobSettingsManager->getAllPairsCached();

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
            $person = $this->personSettingsManager->getByPrimaryKeyCached($personId);

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
            $job = $this->jobManager->getByPrimaryKeyCached($jobId);

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

        $form = $this->person2JobForm->create($personJobSettings);

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

    /**
     * @return PersonJobDeletePersonJobFromEditModal
     */
    protected function createComponentPersonJobDeletePersonJobFromEditModal()
    {
        return $this->personJobModalContainer->getPersonJobDeletePersonJobFromEditModalFactory()->create();
    }

    /**
     * @return PersonJobDeletePersonJobFromListModal
     */
    protected function createComponentPersonJobDeletePersonJobFromListModal()
    {
        return $this->personJobModalContainer->getPersonJobDeletePersonJobFromListModalFactory()->create();
    }
}
