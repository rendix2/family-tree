<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PeopleAdrressForm.php
 * User: Tomáš Babický
 * Date: 07.09.2020
 * Time: 1:18
 */

namespace Rendix2\FamilyTree\App\Forms;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Managers\AddressManager;
use Rendix2\FamilyTree\App\Managers\People2AddressManager;

class PeopleAddressForm extends Control
{
    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * @var People2AddressManager $people2AddressManager
     */
    private $people2AddressManager;

    /**
     * @var AddressManager $addressManager
     */
    private $addressManager;

    /**
     * PeopleAddressForm constructor.
     * @param ITranslator $translator
     * @param AddressManager $addressManager
     * @param People2AddressManager $people2AddressManager
     */
    public function __construct(ITranslator $translator, AddressManager $addressManager, People2AddressManager $people2AddressManager)
    {
        parent::__construct();

        $this->translator = $translator;
        $this->people2AddressManager = $people2AddressManager;
        $this->addressManager = $addressManager;
    }

    /**
     * @return void
     */
    public function render()
    {
        $sep = DIRECTORY_SEPARATOR;

        $this->template->setTranslator($this->translator);
        $this->template->setFile(__DIR__ . $sep .'templates'. $sep. 'peopleAddressForm.latte');

        $addresses = $this->addressManager->getAll();
        $selectedAddresses = $this->people2AddressManager->getPairsByLeft($this->presenter->getParameter('id'));

        $this->template->addFilter('address', new AddressFilter());
        $this->template->addresses = $addresses;
        $this->template->selectedAddresses = array_flip($selectedAddresses);

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
        $data = $form->getHttpData();
        $id = $this->presenter->getParameter('id');

        $this->people2AddressManager->deleteByLeft($id);
        $this->people2AddressManager->addByLeft($id, $data['address']);

        $this->presenter->flashMessage('item_saved', 'success');
        $this->presenter->redirect('edit', $id);
    }
}
