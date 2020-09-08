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
     * @var PeopleManager $peopleManager
     */
    private $peopleManager;

    /**
     * NamePresenter constructor.
     *
     * @param NameManager $manager
     * @param PeopleManager $personManager
     */
    public function __construct(NameManager $manager, PeopleManager $personManager)
    {
        parent::__construct();

        $this->manager = $manager;
        $this->peopleManager = $personManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $names = $this->manager->getAllJoinedPeople();

        $this->template->names = $names;
    }

    /**
     * @param int|null $id
     */
    public function actionEdit($id = null)
    {
        $peoples = $this->peopleManager->getAllPairs();

        $this['form-peopleId']->setItems($peoples);

        $this->traitActionEdit($id);
    }

    /**
     * @param int $id
     */
    public function renderPeople($id)
    {
        $this->template->names = $this->manager->getByPeopleId($id);
    }

    /**
     * @return Form
     */
    public function createComponentForm()
    {
        $form = new Form();

        $form->setTranslator($this->getTranslator());

        $form->addProtection();
        $form->addSelect('peopleId', $this->getTranslator()->translate('name_people'))
            ->setTranslator(null);

        $form->addText('name', 'name_name')
            ->setRequired('name_name_is_required');

        $form->addText('surname', 'name_surname')
            ->setRequired('name_surname_is_required');

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
