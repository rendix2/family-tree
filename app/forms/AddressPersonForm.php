<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressPersonForm.php
 * User: Tomáš Babický
 * Date: 01.09.2020
 * Time: 18:50
 */

namespace Rendix2\FamilyTree\App\Forms;

use Dibi\DateTime;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Managers\AddressManager;
use Rendix2\FamilyTree\App\Managers\Person2AddressManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;

/**
 * Class AddressPersonForm
 *
 * @package Rendix2\FamilyTree\App\Forms
 */
class AddressPersonForm extends Control
{

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var Person2AddressManager $person2AddressManager
     */
    private $person2AddressManager;

    /**
     * @var AddressManager $addressManager
     */
    private $addressManager;

    /**
     * AddressPersonForm constructor.
     * @param ITranslator $translator
     * @param PersonManager $personManager
     * @param Person2AddressManager $person2JobManager
     * @param AddressManager $addressManager
     */
    public function __construct(
        ITranslator $translator,
        PersonManager $personManager,
        Person2AddressManager $person2JobManager,
        AddressManager $addressManager
    ) {
        parent::__construct();

        $this->translator = $translator;
        $this->personManager = $personManager;
        $this->person2AddressManager = $person2JobManager;
        $this->addressManager = $addressManager;
    }

    /**
     * @return void
     */
    public function render()
    {
        $sep = DIRECTORY_SEPARATOR;

        $this->template->setFile(__DIR__ . $sep. 'templates' . $sep . 'addressPersonForm.latte');
        $this->template->setTranslator($this->translator);

        $addressId = $this->presenter->getParameter('id');
        $address = $this->addressManager->getByPrimaryKey($addressId);

        $persons = $this->personManager->getAll();
        $selectedAllPersons = $this->person2AddressManager->getAllByRight($addressId);

        $selectedPersons = [];
        $selectedDates = [];

        foreach ($selectedAllPersons as $person) {
            $selectedDates[$person->personId] = [
                'since' => $person->dateSince,
                'to' => $person->dateTo
            ];

            $selectedPersons[$person->personId] = $person->personId;
        }

        $this->template->address = $address;
        $this->template->persons = $persons;
        $this->template->selectedPersons = $selectedPersons;
        $this->template->selectedDates = $selectedDates;

        $this->template->addFilter('person', new PersonFilter());

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

        $form->onSuccess[] = [$this, 'save'];
        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function save(Form $form, ArrayHash $values)
    {
        $formData = $form->getHttpData();

        $id = $this->presenter->getParameter('id');

        $this->person2AddressManager->deleteByRight($id);

        if (isset($formData['persons'])) {
            foreach ($formData['persons'] as $key => $value) {
                $insertData = [
                    'personId'  => isset($formData['persons'][$key]) ? $formData['persons'][$key] : null,
                    'addressId' => $id,
                    'dateSince' => $formData['dateSince'][$key] ? new DateTime($formData['dateSince'][$key]) : null,
                    'dateTo'    => $formData['dateTo'][$key]    ? new DateTime($formData['dateTo'][$key])    : null,
                ];

                $this->person2AddressManager->addGeneral($insertData);
            }
        }

        $this->presenter->flashMessage('item_saved', 'success');
        $this->presenter->redirect('persons', $id);
    }
}
