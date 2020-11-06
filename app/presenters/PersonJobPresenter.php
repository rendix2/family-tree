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
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Forms\Person2JobForm;
use Rendix2\FamilyTree\App\Managers\JobManager;
use Rendix2\FamilyTree\App\Managers\Person2JobManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Presenters\Traits\PersonJob\EditDeleteModal;
use Rendix2\FamilyTree\App\Presenters\Traits\PersonJob\ListDeleteModal;

/**
 * Class PersonJobPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class PersonJobPresenter extends BasePresenter
{
    use ListDeleteModal;
    use EditDeleteModal;

    /**
     * @var PersonManager
     */
    private $personManager;

    /**
     * @var Person2JobManager $manager
     */
    private $manager;

    /**
     * @var JobManager
     */
    private $jobManager;

    /**
     * PersonJobPresenter constructor.
     * @param PersonManager $personManager
     * @param Person2JobManager $personJobManager
     * @param JobManager $addressManager
     */
    public function __construct(
        PersonManager $personManager,
        Person2JobManager $personJobManager,
        JobManager $addressManager
    ) {
        parent::__construct();

        $this->personManager = $personManager;
        $this->manager = $personJobManager;
        $this->jobManager = $addressManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $relations = $this->manager->getAllJoined();

        $this->template->relations = $relations;

        $this->template->addFilter('person', new PersonFilter($this->getTranslator()));
        $this->template->addFilter('job', new JobFilter());
    }

    /**
     * @param int $personId
     * @param int $jobId
     */
    public function actionEdit($personId, $jobId)
    {
        $persons = $this->personManager->getAllPairs($this->getTranslator());
        $jobs = $this->jobManager->getAllPairs();

        $this['form-personId']->setItems($persons);
        $this['form-jobId']->setItems($jobs);

        if ($personId && $jobId) {
            $relation = $this->manager->getByLeftIdAndRightId($personId, $jobId);

            if (!$relation) {
                $this->error('Item not found.');
            }

            $this['form']->setDefaults($relation);
        } elseif ($personId && !$jobId) {
            $person = $this->personManager->getByPrimaryKey($personId);

            if (!$person) {
                $this->error('Item not found.');
            }

            $this['form-personId']->setValue($personId);
        } elseif (!$personId && $jobId) {
            $job = $this->jobManager->getByPrimaryKey($jobId);

            if (!$job) {
                $this->error('Item not found.');
            }

            $this['form-jobId']->setValue($jobId);
        }
    }

    /**
     * @return Form
     */
    protected function createComponentForm()
    {
        $formFactory = new Person2JobForm($this->getTranslator());
        $form = $formFactory->create();

        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];
        $form->onSuccess[] = [$this, 'saveForm'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveForm(Form $form, ArrayHash $values)
    {
        $personId = $this->getParameter('personId');
        $jobId = $this->getParameter('jobId');

        if ($personId !== null || $jobId !== null) {
            $this->manager->updateGeneral($personId, $jobId, (array)$values);
            $this->flashMessage('item_updated', self::FLASH_SUCCESS);
            $this->redirect('PersonJob:edit', $values->personId, $values->jobId);
        } else {
            $this->manager->addGeneral((array) $values);
            $this->flashMessage('item_added', self::FLASH_SUCCESS);
            $this->redirect('PersonJob:edit', $personId, $jobId);
        }
    }
}
