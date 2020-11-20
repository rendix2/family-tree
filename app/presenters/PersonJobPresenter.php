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
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
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
     * @var Person2JobFacade $person2JobFacade
     */
    private $person2JobFacade;

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
     * @param Person2JobFacade $person2JobFacade
     * @param JobManager $addressManager
     */
    public function __construct(
        PersonManager $personManager,
        Person2JobManager $personJobManager,
        Person2JobFacade $person2JobFacade,
        JobManager $addressManager
    ) {
        parent::__construct();

        $this->personManager = $personManager;
        $this->person2JobFacade = $person2JobFacade;
        $this->manager = $personJobManager;
        $this->jobManager = $addressManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $relations = $this->person2JobFacade->getAllCached();

        $this->template->relations = $relations;

        $this->template->addFilter('person', new PersonFilter($this->getTranslator(), $this->getHttpRequest()));
        $this->template->addFilter('job', new JobFilter());
    }

    /**
     * @param int $personId
     * @param int $jobId
     */
    public function actionEdit($personId, $jobId)
    {
        $persons = $this->personManager->getAllPairsCached($this->getTranslator());
        $jobs = $this->jobManager->getAllPairsCached();

        $this['form-personId']->setItems($persons);
        $this['form-jobId']->setItems($jobs);

        if ($personId && $jobId) {
            $relation = $this->person2JobFacade->getByLeftAndRightCached($personId, $jobId);

            if (!$relation) {
                $this->error('Item not found.');
            }

            $this['form-personId']->setDefaultValue($relation->person->id);
            $this['form-jobId']->setDefaultValue($relation->job->id);

            $this['form-dateSince']->setDefaultValue($relation->duration->dateSince);
            $this['form-dateTo']->setDefaultValue($relation->duration->dateTo);
            $this['form-untilNow']->setDefaultValue($relation->duration->untilNow);

            $this['form']->setDefaults((array)$relation);
        } elseif ($personId && !$jobId) {
            $person = $this->personManager->getByPrimaryKey($personId);

            if (!$person) {
                $this->error('Item not found.');
            }

            $this['form-personId']->setDefaultValue($personId);
        } elseif (!$personId && $jobId) {
            $job = $this->jobManager->getByPrimaryKey($jobId);

            if (!$job) {
                $this->error('Item not found.');
            }

            $this['form-jobId']->setDefaultValue($jobId);
        }
    }

    /**
     * @return Form
     */
    protected function createComponentForm()
    {
        $formFactory = new Person2JobForm($this->getTranslator());

        $form = $formFactory->create();
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
