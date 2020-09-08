<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonJobForm.php
 * User: Tomáš Babický
 * Date: 01.09.2020
 * Time: 17:17
 */

namespace Rendix2\FamilyTree\App\Forms;

use Dibi\DateTime;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Managers\JobManager;
use Rendix2\FamilyTree\App\Managers\People2JobManager;
use Rendix2\FamilyTree\App\Managers\PeopleManager;

/**
 * Class PersonJobForm
 * @package Rendix2\FamilyTree\App\Forms
 */
class PersonJobForm extends Control
{
    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * @var PeopleManager $personManager
     */
    private $personManager;

    /**
     * @var People2JobManager $person2JobManager
     */
    private $person2JobManager;

    /**
     * @var JobManager $jobManager
     */
    private $jobManager;


    /**
     * PersonJobForm constructor.
     * @param ITranslator $translator
     * @param PeopleManager $personManager
     * @param People2JobManager $person2JobManager
     * @param JobManager $jobManager
     */
    public function __construct(
        ITranslator $translator,
        PeopleManager $personManager,
        People2JobManager $person2JobManager,
        JobManager $jobManager
    ) {
        parent::__construct();

        $this->translator = $translator;
        $this->personManager = $personManager;
        $this->person2JobManager = $person2JobManager;
        $this->jobManager = $jobManager;
    }

    /**
     * @return void
     */
    public function render()
    {
        $sep = DIRECTORY_SEPARATOR;

        $this->template->setFile(__DIR__ . $sep . 'templates' . $sep . 'personJobForm.latte');
        $this->template->setTranslator($this->translator);

        $personId = $this->presenter->getParameter('id');

        $person = $this->personManager->getByPrimaryKey($personId);

        $jobs = $this->jobManager->getAll();
        $selectedAllJobs = $this->person2JobManager->getAllByLeft($personId);

        $selectedJobs = [];
        $selectedDates = [];

        foreach ($selectedAllJobs as $job) {
            $selectedDates[$job->peopleId] = [
                'since' => $job->dateSince,
                'to' => $job->dateTo
            ];

            $selectedJobs[$job->peopleId] = $job->peopleId;
        }

        $this->template->jobs = $jobs;
        $this->template->person = $person;
        $this->template->selectedJobs = $selectedJobs;
        $this->template->selectedDates = $selectedDates;

        $this->template->render();
    }

    /**
     * @return Form
     */
    public function createComponentForm()
    {
        $form = new Form();

        $form->setTranslator($this->translator);

        $form->addProtection();

        $form->addSubmit('send', 'save');

        $form->onSuccess[] = [$this, 'saveForm'];
        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveForm(Form $form, ArrayHash $values)
    {
        $formData = $form->getHttpData();

        $id = $this->presenter->getParameter('id');

        $this->person2JobManager->deleteByLeft($id);

        if (isset($formData['jobs'])) {
            foreach ($formData['jobs'] as $key => $value) {
                $insertData = [
                    'peopleId'  => $id,
                    'jobId'     => $formData['jobs'][$key],
                    'dateSince' => $formData['dateSince'][$key] ? new DateTime($formData['dateSince'][$key]) : null,
                    'dateTo'    => $formData['dateTo'][$key]    ? new DateTime($formData['dateTo'][$key])    : null,
                ];

                $this->person2JobManager->addGeneral($insertData);
            }
        }

        $this->presenter->flashMessage('item_saved', 'success');
        $this->presenter->redirect('jobs', $id);
    }
}
