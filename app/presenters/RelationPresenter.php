<?php
/**
 *
 * Created by PhpStorm.
 * Filename: RelationPresenter.php
 * User: Tomáš Babický
 * Date: 29.08.2020
 * Time: 20:22
 */

namespace Rendix2\FamilyTree\App\Presenters;

use Dibi\Row;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Facades\RelationFacade;
use Rendix2\FamilyTree\App\Filters\DurationFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\RelationManager;

/**
 * Class RelationPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class RelationPresenter extends BasePresenter
{
    use CrudPresenter {
        actionEdit as traitActionEdit;
    }

    /**
     * @var RelationFacade $relationFacade
     */
    private $relationFacade;

    /**
     * @var RelationManager $manager
     */
    private $manager;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var Row $person
     */
    private $person;

    /**
     * RelationPresenter constructor.
     *
     * @param RelationFacade $relationFacade
     * @param RelationManager $manager
     * @param PersonManager $personManager
     */
    public function __construct(
        RelationFacade $relationFacade,
        RelationManager $manager,
        PersonManager $personManager
    ) {
        parent::__construct();

        $this->relationFacade = $relationFacade;
        $this->manager = $manager;
        $this->personManager = $personManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $relations = $this->relationFacade->getAllCached();

        $this->template->relations = $relations;

        $this->template->addFilter('person', new PersonFilter($this->getTranslator(), $this->getHttpRequest()));
        $this->template->addFilter('duration', new DurationFilter($this->getTranslator()));
    }

    /**
     * @param int|null $id relationId
     */
    public function actionEdit($id = null)
    {
        $persons = $this->personManager->getAllPairsCached($this->getTranslator());

        $this['form-maleId']->setItems($persons);
        $this['form-femaleId']->setItems($persons);

        if ($id !== null) {
            $relation = $this->relationFacade->getByPrimaryKeyCached($id);

            if (!$relation) {
                $this->error('Item not found.');
            }

            $this['form']->setDefaults((array)$relation);
            $this['form-maleId']->setDefaultValue($relation->male->id);
            $this['form-femaleId']->setDefaultValue($relation->female->id);

            $this['form-dateSince']->setDefaultValue($relation->duration->dateSince);
            $this['form-dateTo']->setDefaultValue($relation->duration->dateTo);
            $this['form-untilNow']->setDefaultValue($relation->duration->untilNow);
        }
    }

    /**
     * @param int|null $id relationId
     */
    public function renderEdit($id = null)
    {
        if ($id === null) {
            $this->template->femaleRelationAge = null;
            $this->template->maleRelationAge = null;
            $this->template->relationLength = null;
        } else {
            $relation = $this->relationFacade->getByPrimaryKeyCached($id);

            $calcResult = $this->manager->calcLengthRelation($relation->male, $relation->female, $relation->duration, $this->getTranslator());

            $femaleWeddingAge = $calcResult['femaleRelationAge'];
            $maleWeddingAge = $calcResult['maleRelationAge'];
            $relationLength = $calcResult['relationLength'];

            $this->template->female = $relation->female;
            $this->template->femaleRelationAge = $femaleWeddingAge;

            $this->template->male = $relation->male;
            $this->template->maleRelationAge = $maleWeddingAge;

            $this->template->relationLength = $relationLength;

            $this->template->addFilter('person', new PersonFilter($this->getTranslator(), $this->getHttpRequest()));
        }
    }

    /**
     * @param int|null $id personId
     */
    public function actionMale($id = null)
    {
        $female = $this->personManager->getByPrimaryKey($id);

        if (!$female) {
            $this->error('Item not found.');
        }

        $this->person = $female;

        $partners = $this->personManager->getAllPairs($this->getTranslator());

        $personFilter = new PersonFilter($this->getTranslator(), $this->getHttpRequest());

        $this['maleForm-maleId']->setItems($partners);

        $this['maleForm-femaleId']->setItems([$id => $personFilter($female)]);
        $this['maleForm-femaleId']->setDisabled()->setDefaultValue($id);
    }

    /**
     * @param int|null $id personId
     */
    public function renderMale($id = null)
    {
        $this->template->person = $this->person;

        $this->template->addFilter('person', new PersonFilter($this->getTranslator(), $this->getHttpRequest()));
    }

    /**
     * @param int|null $id personId
     */
    public function actionFemale($id = null)
    {
        $male = $this->personManager->getByPrimaryKey($id);

        if (!$male) {
            $this->error('Item not found.');
        }

        $this->person = $male;

        $partners = $this->personManager->getAllPairs($this->getTranslator());

        $personFilter = new PersonFilter($this->getTranslator(), $this->getHttpRequest());

        $this['femaleForm-femaleId']->setItems($partners);

        $this['femaleForm-maleId']->setItems([$id => $personFilter($male)]);
        $this['femaleForm-maleId']->setDisabled()->setDefaultValue($id);
    }

    /**
     * @param int|null $id personId
     */
    public function renderFemale($id = null)
    {
        $this->template->person = $this->person;

        $this->template->addFilter('person', new PersonFilter($this->getTranslator(), $this->getHttpRequest()));
    }

    /**
     * @return Form
     */
    private function createForm()
    {
        $form = new Form();

        $form->setTranslator($this->getTranslator());

        $form->addProtection();

        $form->addSelect('maleId', $this->getTranslator()->translate('relation_male'))
            ->setTranslator(null)
            ->setPrompt($this->getTranslator()->translate('relation_select_male'))
            ->setRequired('relation_male_required');

        $form->addSelect('femaleId', $this->getTranslator()->translate('relation_female'))
            ->setTranslator(null)
            ->setPrompt($this->getTranslator()->translate('relation_select_female'))
            ->setRequired('relation_female_required');

        $form->addCheckbox('untilNow', 'relation_until_now')
            ->addCondition(Form::EQUAL, false)
            ->toggle('date-to');

        $form->addTbDatePicker('dateSince', 'date_since')
            ->setNullable()
            ->setHtmlAttribute('class', 'form-control datepicker')
            ->setHtmlAttribute('data-toggle', 'datepicker')
            ->setHtmlAttribute('data-target', '#date');

        $form->addTbDatePicker('dateTo', 'date_to')
            ->setNullable()
            ->setOption('id', 'date-to')
            ->setHtmlAttribute('class', 'form-control datepicker')
            ->setHtmlAttribute('data-toggle', 'datepicker')
            ->setHtmlAttribute('data-target', '#date');

        $form->addSubmit('send', 'relation_save_relation');

        return $form;
    }

    /**
     * @return Form
     */
    protected function createComponentForm()
    {
        $form = $this->createForm();

        $form->onSuccess[] = [$this, 'saveForm'];
        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }

    //// MALE

    /**
     * @return Form
     */
    protected function createComponentMaleForm()
    {
        $form = $this->createForm();

        $form->onSuccess[] = [$this, 'saveMaleForm'];
        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveMaleForm(Form $form, ArrayHash $values)
    {
        $values->femaleId = $this->getParameter('id');
        $id = $this->manager->add($values);
        $this->flashMessage('item_added', self::FLASH_SUCCESS);
        $this->redirect(':edit', $id);
    }

    /// FEMALE

    /**
     * @return Form
     */
    protected function createComponentFemaleForm()
    {
        $form = $this->createForm();

        $form->onSuccess[] = [$this, 'saveFemaleForm'];
        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveFemaleForm(Form $form, ArrayHash $values)
    {
        $values->maleId = $this->getParameter('id');
        $id = $this->manager->add($values);
        $this->flashMessage('item_added', self::FLASH_SUCCESS);
        $this->redirect(':edit', $id);
    }
}
