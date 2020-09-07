<?php
/**
 *
 * Created by PhpStorm.
 * Filename: JobPeopleForm.php
 * User: Tomáš Babický
 * Date: 01.09.2020
 * Time: 16:06
 */

namespace Rendix2\FamilyTree\App\Forms;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Managers\People2JobManager;
use Rendix2\FamilyTree\App\Managers\PeopleManager;

class JobPeopleForm extends Control
{
    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * @var People2JobManager $people2JobManager
     */
    private $people2JobManager;

    /**
     * @var PeopleManager $peopleManager
     */
    private $peopleManager;

    /**
     * JobPeopleForm constructor.
     *
     * @param ITranslator $translator
     * @param People2JobManager $people2JobManager
     * @param PeopleManager $peopleManager
     */
    public function __construct(ITranslator $translator, People2JobManager $people2JobManager, PeopleManager $peopleManager)
    {
        parent::__construct();

        $this->translator = $translator;
        $this->people2JobManager = $people2JobManager;
        $this->peopleManager = $peopleManager;
    }

    /**
     * @return void
     */
    public function render()
    {
        $sep = DIRECTORY_SEPARATOR;

        $this->template->setFile(__DIR__ . $sep. 'templates' . $sep . 'job2People.latte');
        $this->template->setTranslator($this->translator);

        $peoples = $this->peopleManager->getAll();
        $selectedPeoples = $this->people2JobManager->getPairsByRight($this->presenter->getParameter('id'));

        $this->template->peoples = $peoples;
        $this->template->peoplesSelected = array_flip($selectedPeoples);

        $this->template->render();
    }

    /**
     * @return Form
     */
    public function createComponentForm()
    {
        $form = new Form();

        $form->setTranslator($this->translator);

        $form->addProtection();
        $form->addSubmit('send', 'save');

        $form->onSuccess[] = [$this, 'saveForm'];
        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveForm(Form $form, ArrayHash $values)
    {
        $formData = $form->getHttpData();

        $id = $this->presenter->getParameter('id');

        $this->people2JobManager->deleteByRight($id);
        $this->people2JobManager->addByRight($id, $formData['peoples']);

        $this->presenter->flashMessage('Item saved.', 'success');
        $this->presenter->redirect('Job:Peoples', $id);
    }
}
