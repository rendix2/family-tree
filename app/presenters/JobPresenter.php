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
use Rendix2\FamilyTree\App\Managers\JobManager;

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
     * JobPresenter constructor.
     *
     * @param JobManager $manager
     */
    public function __construct(JobManager $manager)
    {
        parent::__construct();

        $this->manager = $manager;
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
}
