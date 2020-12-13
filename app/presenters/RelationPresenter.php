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
use Rendix2\FamilyTree\App\Facades\RelationFacade;
use Rendix2\FamilyTree\App\Filters\DurationFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Filters\RelationFilter;
use Rendix2\FamilyTree\App\Forms\RelationForm;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\RelationManager;
use Rendix2\FamilyTree\App\Presenters\Traits\Relation\RelationDeleteRelationFromEditModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Relation\RelationDeleteRelationFromListModal;

/**
 * Class RelationPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class RelationPresenter extends BasePresenter
{
    use RelationDeleteRelationFromEditModal;
    use RelationDeleteRelationFromListModal;

    /**
     * @var RelationFacade $relationFacade
     */
    private $relationFacade;

    /**
     * @var RelationManager $relationManager
     */
    private $relationManager;

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
        $this->relationManager = $manager;
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

        $this['relationForm-maleId']->setItems($persons);
        $this['relationForm-femaleId']->setItems($persons);

        if ($id !== null) {
            $relation = $this->relationFacade->getByPrimaryKeyCached($id);

            if (!$relation) {
                $this->error('Item not found.');
            }

            $this['relationForm']->setDefaults((array) $relation);
            $this['relationForm-maleId']->setDefaultValue($relation->male->id);
            $this['relationForm-femaleId']->setDefaultValue($relation->female->id);

            $this['relationForm-dateSince']->setDefaultValue($relation->duration->dateSince);
            $this['relationForm-dateTo']->setDefaultValue($relation->duration->dateTo);
            $this['relationForm-untilNow']->setDefaultValue($relation->duration->untilNow);
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

            $calcResult = $this->relationManager->calcLengthRelation($relation->male, $relation->female, $relation->duration, $this->getTranslator());

            $femaleWeddingAge = $calcResult['femaleRelationAge'];
            $maleWeddingAge = $calcResult['maleRelationAge'];
            $relationLength = $calcResult['relationLength'];

            $this->template->female = $relation->female;
            $this->template->femaleRelationAge = $femaleWeddingAge;

            $this->template->male = $relation->male;
            $this->template->maleRelationAge = $maleWeddingAge;

            $this->template->relationLength = $relationLength;
            $this->template->relation = $relation;

            $personFilter = new PersonFilter($this->getTranslator(), $this->getHttpRequest());

            $this->template->addFilter('person', $personFilter);
            $this->template->addFilter('relation', new RelationFilter($personFilter));
        }
    }

    /**
     * @return Form
     */
    protected function createComponentRelationForm()
    {
        $formFactory = new RelationForm($this->getTranslator());

        $form = $formFactory->create();
        $form->onSuccess[] = [$this, 'relationFormSuccess'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function relationFormSuccess(Form $form, ArrayHash $values)
    {
        $id = $this->getParameter('id');

        if ($id) {
            $this->relationManager->updateByPrimaryKey($id, $values);

            $this->flashMessage('relation_saved', self::FLASH_SUCCESS);
        } else {
            $id = $this->relationManager->add($values);

            $this->flashMessage('relation_added', self::FLASH_SUCCESS);
        }

        $this->redirect('Relation:edit', $id);
    }
}
