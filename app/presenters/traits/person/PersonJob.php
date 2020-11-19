<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonJob.php
 * User: Tomáš Babický
 * Date: 26.10.2020
 * Time: 1:35
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\Person2JobForm;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Trait PersonJob
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonJob
{
    /**
     * @param int $id personId
     */
    public function actionJob($id)
    {
        $person = $this->manager->getByPrimaryKey($id);

        if (!$person) {
            $this->error('Item not found.');
        }

        $this->item = $person;

        $jobs = $this->jobManager->getAllPairs();

        $personFilter = new PersonFilter($this->getTranslator(), $this->getHttpRequest());

        $this['jobForm-personId']->setItems([$id => $personFilter($person)])->setDisabled()->setDefaultValue($id);
        $this['jobForm-jobId']->setItems($jobs);
    }

    /**
     * @param int $id personId
     */
    public function renderJob($id)
    {
        $this->template->person = $this->item;

        $this->template->addFilter('person', new PersonFilter($this->getTranslator(), $this->getHttpRequest()));
    }

    /**
     * @return Form
     */
    public function createComponentJobForm()
    {
        $formFactory = new Person2JobForm($this->getTranslator());
        $form = $formFactory->create();

        $form->onSuccess[] = [$this, 'saveJobForm'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveJobForm(Form $form, ArrayHash $values)
    {
        $personId = $this->getParameter('id');

        $values->personId = $personId;

        $this->person2JobManager->addGeneral((array)$values);

        $this->flashMessage('item_added', BasePresenter::FLASH_SUCCESS);
        $this->redirect(':edit', $personId);
    }
}
