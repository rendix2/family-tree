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

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Modals\Relation\Container\RelationModalContainer;
use Rendix2\FamilyTree\App\Controls\Modals\Relation\RelationDeleteRelationFromEditModal;
use Rendix2\FamilyTree\App\Controls\Modals\Relation\RelationDeleteRelationFromListModal;
use Rendix2\FamilyTree\App\Facades\RelationFacade;
use Rendix2\FamilyTree\App\Forms\RelationForm;
use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;
use Rendix2\FamilyTree\App\Managers\RelationManager;

/**
 * Class RelationPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class RelationPresenter extends BasePresenter
{
    /**
     * @var RelationFacade $relationFacade
     */
    private $relationFacade;

    /**
     * @var RelationManager $relationManager
     */
    private $relationManager;

    /**
     * @var RelationModalContainer $relationModalContainer
     */
    private $relationModalContainer;

    /**
     * @var PersonSettingsManager $personSettingsManager
     */
    private $personSettingsManager;

    /**
     * RelationPresenter constructor.
     *
     * @param RelationFacade $relationFacade
     * @param RelationModalContainer $relationModalContainer
     * @param RelationManager $manager
     * @param PersonSettingsManager $personSettingsManager
     */
    public function __construct(
        RelationFacade $relationFacade,
        RelationModalContainer $relationModalContainer,
        RelationManager $manager,
        PersonSettingsManager $personSettingsManager
    ) {
        parent::__construct();

        $this->relationModalContainer = $relationModalContainer;

        $this->relationFacade = $relationFacade;

        $this->relationManager = $manager;

        $this->personSettingsManager = $personSettingsManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $relations = $this->relationFacade->getAllCached();

        $this->template->relations = $relations;
    }

    /**
     * @param int|null $id relationId
     */
    public function actionEdit($id = null)
    {
        $persons = $this->personSettingsManager->getAllPairsCached($this->translator);

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

            $relationLengthArray = $this->relationManager->getRelationLength($relation->male, $relation->female, $relation->duration, $this->translator);

            $femaleWeddingAge = $relationLengthArray['femaleRelationAge'];
            $maleWeddingAge = $relationLengthArray['maleRelationAge'];
            $relationLength = $relationLengthArray['relationLength'];

            $this->template->female = $relation->female;
            $this->template->femaleRelationAge = $femaleWeddingAge;

            $this->template->male = $relation->male;
            $this->template->maleRelationAge = $maleWeddingAge;

            $this->template->relationLength = $relationLength;
            $this->template->relation = $relation;
        }
    }

    /**
     * @return Form
     */
    protected function createComponentRelationForm()
    {
        $formFactory = new RelationForm($this->translator);

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

    /**
     * @return RelationDeleteRelationFromEditModal
     */
    protected function createComponentRelationDeleteRelationFromEditModal()
    {
        return $this->relationModalContainer->getRelationDeleteRelationFromEditModalFactory()->create();
    }

    /**
     * @return RelationDeleteRelationFromListModal
     */
    protected function createComponentRelationDeleteRelationFromListModal()
    {
        return $this->relationModalContainer->getRelationDeleteRelationFromListModalFactory()->create();
    }
}
