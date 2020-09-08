<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddressForm.php
 * User: Tomáš Babický
 * Date: 07.09.2020
 * Time: 1:18
 */

namespace Rendix2\FamilyTree\App\Forms;

use Dibi\DateTime;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Managers\AddressManager;
use Rendix2\FamilyTree\App\Managers\People2AddressManager;
use Rendix2\FamilyTree\App\Managers\PeopleManager;

class PersonAddressForm extends Control
{
    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * @var PeopleManager $personManager
     */
    private $personManager;

    /**
     * @var People2AddressManager $person2AddressManager
     */
    private $person2AddressManager;

    /**
     * @var AddressManager $addressManager
     */
    private $addressManager;

    /**
     * PersonAddressForm constructor.
     * @param ITranslator $translator
     * @param PeopleManager $personManager
     * @param People2AddressManager $person2AddressManager
     * @param AddressManager $addressManager
     */
    public function __construct(
        ITranslator $translator,
        PeopleManager $personManager,
        People2AddressManager $person2AddressManager,
        AddressManager $addressManager)
    {
        parent::__construct();

        $this->translator = $translator;
        $this->personManager = $personManager;
        $this->person2AddressManager = $person2AddressManager;
        $this->addressManager = $addressManager;
    }

    /**
     * @return void
     */
    public function render()
    {
        $sep = DIRECTORY_SEPARATOR;

        $this->template->setFile(__DIR__ . $sep .'templates'. $sep. 'personAddressForm.latte');
        $this->template->setTranslator($this->translator);

        $personId = $this->presenter->getParameter('id');

        $person = $this->personManager->getByPrimaryKey($personId);
        $addresses = $this->addressManager->getAll();
        $selectedAllAddresses = $this->person2AddressManager->getAllByLeft($personId);

        $selectedDates = [];
        $selectedAddresses = [];

        foreach ($selectedAllAddresses as $address) {
            $selectedDates[$address->addressId] = [
                'since' => $address->dateSince,
                'to' => $address->dateTo
            ];

            $selectedAddresses[$address->addressId] = $address->addressId;
        }

        $this->template->addFilter('address', new AddressFilter());
        $this->template->addresses = $addresses;
        $this->template->selectedAddresses = $selectedAddresses;
        $this->template->selectedDates = $selectedDates;
        $this->template->person = $person;

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

        $this->person2AddressManager->deleteByLeft($id);

        if (isset($formData['address'])) {
            foreach ($formData['address'] as $key => $value) {
                $insertData = [
                    'peopleId'  => $id,
                    'addressId' => $formData['address'][$key],
                    'dateSince' => $formData['dateSince'][$key] ? new DateTime($formData['dateSince'][$key]) : null,
                    'dateTo'    => $formData['dateTo'][$key]    ? new DateTime($formData['dateTo'][$key])    : null,
                ];

                $this->person2AddressManager->addGeneral($insertData);
            }
        }

        $this->presenter->flashMessage('item_saved', 'success');
        $this->presenter->redirect('addresses', $id);
    }
}