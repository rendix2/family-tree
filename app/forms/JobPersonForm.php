<?php
/**
 *
 * Created by PhpStorm.
 * Filename: JobPersonForm.php
 * User: Tomáš Babický
 * Date: 01.09.2020
 * Time: 16:06
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

class JobPersonForm extends Control
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
     * JobPersonForm constructor.
     *
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

        $this->template->setFile(__DIR__ . $sep. 'templates' . $sep . 'jobPersonForm.latte');
        $this->template->setTranslator($this->translator);

        $jobId = $this->presenter->getParameter('id');

        $job = $this->jobManager->getByPrimaryKey($jobId);
        $persons = $this->personManager->getAll();
        $selectedAllPersons = $this->person2JobManager->getAllByRight($jobId);

        $selectedDates = [];
        $selectedPersons = [];

        foreach ($selectedAllPersons as $person) {
            $selectedDates[$person->peopleId] = [
                'since' => $person->dateSince,
                'to' => $person->dateTo
            ];

            $selectedPersons[$person->peopleId] = $person->peopleId;
        }

        $this->template->persons = $persons;
        $this->template->selectedDates = $selectedDates;
        $this->template->selectedPersons = $selectedPersons;
        $this->template->job = $job;

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

        $form->onSuccess[] = [$this, 'save'];
        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }

    /**
     * @param int    $id
     * @param array  $data
     * @param string $key
     *
     * @return array
     */
    private function createRow($id, $data, $key)
    {
        return [
            'peopleId'  => isset($data['persons'][$key]) ? $data['persons'][$key] : null,
            'jobId' => $id,
            'dateSince' => $data['dateSince'][$key] ? new DateTime($data['dateSince'][$key]) : null,
            'dateTo'    => $data['dateTo'][$key]    ? new DateTime($data['dateTo'][$key])    : null,
        ];
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function save(Form $form, ArrayHash $values)
    {
        $formData = $form->getHttpData();

        $id = $this->presenter->getParameter('id');

        $this->person2JobManager->deleteByRight($id);

        if (isset($formData['persons'])) {
            foreach ($formData['persons'] as $key => $value) {
                $insertData = $this->createRow($id, $formData, $key);

                $this->person2JobManager->addGeneral($insertData);
            }
        }

        $this->presenter->flashMessage('Item saved.', 'success');
        $this->presenter->redirect('Job:Persons', $id);
    }
}
