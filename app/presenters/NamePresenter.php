<?php
/**
 *
 * Created by PhpStorm.
 * Filename: NamePresenter.php
 * User: Tomáš Babický
 * Date: 29.08.2020
 * Time: 2:09
 */

namespace Rendix2\FamilyTree\App\Presenters;

use Nette\Application\UI\Form;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Filters\NameFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Managers\GenusManager;
use Rendix2\FamilyTree\App\Managers\NameManager;
use Rendix2\FamilyTree\App\Managers\PeopleManager;

/**
 * Class NamePresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class NamePresenter extends BasePresenter
{
    use CrudPresenter {
        actionEdit as traitActionEdit;
    }

    /**
     * @var NameManager $manager
     */
    private $manager;

    /**
     * @var GenusManager $genusManager
     */
    private $genusManager;

    /**
     * @var PeopleManager $personManager
     */
    private $personManager;

    /**
     * NamePresenter constructor.
     *
     * @param GenusManager $genusManager
     * @param NameManager $manager
     * @param PeopleManager $personManager
     */
    public function __construct(
        GenusManager $genusManager,
        NameManager $manager,
        PeopleManager $personManager
    ) {
        parent::__construct();

        $this->manager = $manager;
        $this->genusManager = $genusManager;
        $this->personManager = $personManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $names = $this->manager->getAllJoinedPerson();

        $this->template->names = $names;
    }

    /**
     * @param int|null $id
     */
    public function actionEdit($id = null)
    {
        $persons = $this->personManager->getAllPairs();
        $genuses = $this->genusManager->getPairs('surname');

        $this['form-peopleId']->setItems($persons);
        $this['form-genusId']->setItems($genuses);

        $this->traitActionEdit($id);
    }

    /**
     * @param int $id
     */
    public function renderPerson($id)
    {
        $person = $this->personManager->getByPrimaryKey($id);
        $names = $this->manager->getByPersonId($id);

        $this->template->person = $person;
        $this->template->names = $names;

        $this->template->addFilter('name', new NameFilter());
        $this->template->addFilter('person', new PersonFilter());
    }

    /**
     * @return Form
     */
    public function createComponentForm()
    {
        $form = new Form();

        $form->setTranslator($this->getTranslator());

        $form->addProtection();

        $form->addSelect('peopleId', $this->getTranslator()->translate('name_person'))
            ->setTranslator(null)
            ->setPrompt($this->getTranslator()->translate('name_select_person'))
            ->setRequired('name_person_required');

        $form->addText('name', 'name_name')
            ->setRequired('name_name_required');

        $form->addText('nameFonetic', 'name_name_fonetic')
            ->setNullable();

        $form->addText('surname', 'name_surname')
            ->setRequired('name_surname_required');

        $form->addSelect('genusId', 'name_genus')
            ->setPrompt($this->getTranslator()->translate('name_select_genus'))
            ->setTranslator(null)
            ->setRequired('name_genus_required');

        $form->addTbDatePicker('dateSince', 'date_since')
            ->setNullable()
            ->setHtmlAttribute('class', 'form-control datepicker')
            ->setHtmlAttribute('data-toggle', 'datepicker')
            ->setHtmlAttribute('data-target', '#date');

        $form->addTbDatePicker('dateTo', 'date_to')
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
