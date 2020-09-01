<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PeopleJobForm.php
 * User: Tomáš Babický
 * Date: 01.09.2020
 * Time: 17:17
 */

namespace Rendix2\FamilyTree\App\Forms;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Managers\JobManager;
use Rendix2\FamilyTree\App\Managers\People2JobManager;
use Rendix2\FamilyTree\App\Managers\PeopleManager;

/**
 * Class PeopleJobForm
 * @package Rendix2\FamilyTree\App\Forms
 */
class PeopleJobForm extends Control
{
    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * @var People2JobManager $people2JobManager
     */
    private $people2JobManager;

    /**
     * @var JobManager $jobManager
     */
    private $jobManager;


    public function __construct(ITranslator $translator, JobManager $jobManager, People2JobManager $people2JobManager)
    {
        parent::__construct();

        $this->translator = $translator;
        $this->jobManager = $jobManager;
        $this->people2JobManager = $people2JobManager;
    }

    public function render()
    {
        $sep = DIRECTORY_SEPARATOR;

        $this->template->setFile(__DIR__ . $sep . 'templates' . $sep . 'people2Job.latte');

        $jobs = $this->jobManager->getAll();
        $selectedJobs = $this->people2JobManager->getPairsByLeft($this->presenter->getParameter('id'));

        $this->template->jobs = $jobs;
        $this->template->selectedJobs = array_flip($selectedJobs);

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

        return $form;
    }

    public function saveForm(Form $form, ArrayHash $values)
    {
        $formData = $form->getHttpData();

        $id = $this->presenter->getParameter('id');

        $this->people2JobManager->deleteByLeft($id);
        $this->people2JobManager->addByLeft($id, $formData['job']);
    }
}
