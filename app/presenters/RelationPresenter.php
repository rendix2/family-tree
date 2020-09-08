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
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Managers\PeopleManager;
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
     * @var RelationManager $manager
     */
    private $manager;

    /**
     * @var PeopleManager $peopleManager
     */
    private $peopleManager;

    /**
     * RelationPresenter constructor.
     *
     * @param RelationManager $manager
     * @param PeopleManager $personManager
     */
    public function __construct(RelationManager $manager, PeopleManager $personManager)
    {
        parent::__construct();

        $this->manager = $manager;
        $this->peopleManager = $personManager;
    }

    /**
     * @param int|null $id
     */
    public function actionEdit($id = null)
    {
        $males = $this->peopleManager->getMalesPairs();
        $females = $this->peopleManager->getFemalesPairs();

        $this['form-maleId']->setItems($males);
        $this['form-femaleId']->setItems($females);

        $this->traitActionEdit($id);
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $relations = $this->manager->getFluentBothJoined();

        $this->template->relations = $relations;
    }

    /**
     * @return Form
     */
    public function createComponentForm()
    {
        $form = new Form();

        $form->setTranslator($this->getTranslator());

        $form->addProtection();

        $form->addSelect('maleId', $this->getTranslator()->translate('relation_male'))
            ->setTranslator(null);

        $form->addSelect('femaleId', $this->getTranslator()->translate('relation_female'))
            ->setTranslator(null);

        $form->addTbDatePicker('dateSince', 'wedding_date_since')
            ->setNullable()
            ->setHtmlAttribute('class', 'form-control datepicker')
            ->setHtmlAttribute('data-toggle', 'datepicker')
            ->setHtmlAttribute('data-target', '#date');

        $form->addTbDatePicker('dateTo', 'wedding_date_to')
            ->setNullable()
            ->setHtmlAttribute('class', 'form-control datepicker')
            ->setHtmlAttribute('data-toggle', 'datepicker')
            ->setHtmlAttribute('data-target', '#date');

        $form->addSubmit('send', 'save');

        $form->onSuccess[] = [$this, 'saveForm'];
        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }
}
