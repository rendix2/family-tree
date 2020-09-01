<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AdressPeopleForm.php
 * User: Tomáš Babický
 * Date: 01.09.2020
 * Time: 18:50
 */

namespace Rendix2\FamilyTree\App\Forms;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Managers\People2AddressManager;
use Rendix2\FamilyTree\App\Managers\People2JobManager;
use Rendix2\FamilyTree\App\Managers\PeopleManager;

/**
 * Class AddressPeopleForm
 *
 * @package Rendix2\FamilyTree\App\Forms
 */
class AddressPeopleForm extends Control
{

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * @var PeopleManager $peopleManager
     */
    private $peopleManager;

    /**
     * @var People2AddressManager $people2AddressManager
     */
    private $people2AddressManager;

    /**
     * AddressPeopleForm constructor.
     * @param ITranslator $translator
     * @param PeopleManager $peopleManager
     * @param People2AddressManager $people2JobManager
     */
    public function __construct(ITranslator $translator, PeopleManager $peopleManager, People2AddressManager $people2JobManager)
    {
        parent::__construct();

        $this->translator = $translator;
        $this->peopleManager = $peopleManager;
        $this->people2AddressManager = $people2JobManager;
    }

    /**
     *
     */
    public function render()
    {
        $sep = DIRECTORY_SEPARATOR;

        $this->template->setFile(__DIR__ . $sep . 'templates' . $sep. 'addressPeople.latte');

        $peoples = $this->peopleManager->getAll();
        $selectedPeople = $this->people2AddressManager->getPairsByRight($this->presenter->getParameter('id'));

        $this->template->peoples = $peoples;
        $this->template->selectedPeoples = array_flip($selectedPeople);

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

        $this->people2AddressManager->deleteByRight($id);
        $this->people2AddressManager->addByRight($id, $formData['people']);
    }
}
