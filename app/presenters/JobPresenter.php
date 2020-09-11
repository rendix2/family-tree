<?php
/**
 *
 * Created by PhpStorm.
 * Filename: JobPresenter.php
 * User: Tomáš Babický
 * Date: 29.08.2020
 * Time: 22:29
 */

namespace Rendix2\FamilyTree\App\Presenters;

use Nette\Application\UI\Form;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Forms\JobPersonForm;
use Rendix2\FamilyTree\App\Managers\JobManager;
use Rendix2\FamilyTree\App\Managers\People2JobManager;
use Rendix2\FamilyTree\App\Managers\PeopleManager;

/**
 * Class JobPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class JobPresenter extends BasePresenter
{
    use CrudPresenter;

    /**
     * @var JobManager $manager
     */
    private $manager;

    /**
     * @var People2JobManager $person2JobManager
     */
    private $person2JobManager;

    /**
     * @var PeopleManager $personManager
     */
    private $personManager;

    /**
     * JobPresenter constructor.
     *
     * @param JobManager $manager
     * @param People2JobManager $person2JobManager
     * @param PeopleManager $personManager
     */
    public function __construct(JobManager $manager, People2JobManager $person2JobManager, PeopleManager $personManager)
    {
        parent::__construct();

        $this->manager = $manager;
        $this->person2JobManager = $person2JobManager;
        $this->personManager = $personManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $jobs = $this->manager->getAll();

        $this->template->jobs = $jobs;
    }

    /**
     * @param $id
     */
    public function renderEdit($id)
    {
        $persons = $this->person2JobManager->getAllByRightJoined($id);

        $this->template->persons = $persons;
    }

    /**
     * @param int $id
     */
    public function actionPersons($id)
    {
        $job = $this->manager->getByPrimaryKey($id);

        if (!$job) {
            $this->error('Job does not found.');
        }
    }

    public function renderPersons($id)
    {
    }

    /**
     * @return Form
     */
    public function createComponentForm()
    {
        $form = new Form();

        $form->setTranslator($this->getTranslator());

        $form->addProtection();

        $form->addText('name', 'job_name')
            ->setRequired('job_name_is_required');

        $form->addSubmit('send', 'save');

        $form->onSuccess[] = [$this, 'saveForm'];
        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }

    /**
     * @return JobPersonForm
     */
    public function createComponentPersonsForm()
    {
        return new JobPersonForm(
            $this->getTranslator(),
            $this->personManager,
            $this->person2JobManager,
            $this->manager
        );
    }
}
