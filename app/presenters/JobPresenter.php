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
use Rendix2\FamilyTree\App\Forms\JobPeopleForm;
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
     * @var People2JobManager $people2JobManager
     */
    private $people2JobManager;
    /**
     * @var PeopleManager $peopleManager
     */
    private $peopleManager;

    /**
     * JobPresenter constructor.
     *
     * @param JobManager $manager
     * @param People2JobManager $people2JobManager
     * @param PeopleManager $peopleManager
     */
    public function __construct(JobManager $manager, People2JobManager $people2JobManager, PeopleManager $peopleManager)
    {
        parent::__construct();

        $this->manager = $manager;
        $this->people2JobManager = $people2JobManager;
        $this->peopleManager = $peopleManager;
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
        $peoples = $this->people2JobManager->getAllByRightJoined($id);

        $this->template->peoples = $peoples;
    }

    /**
     * @param int $id
     */
    public function actionPeoples($id)
    {
        $job = $this->manager->getByPrimaryKey($id);

        if (!$job) {
            $this->error('Job does not found.');
        }
    }

    public function renderPeoples($id)
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
        $form->addText('name', 'job_name');
        $form->addSubmit('send', 'save');

        $form->onSuccess[] = [$this, 'saveForm'];

        return $form;
    }

    public function createComponentPeopleForm()
    {
        return new JobPeopleForm($this->getTranslator(), $this->people2JobManager, $this->peopleManager);
    }

}
