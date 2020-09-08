<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AdressPresenter.php
 * User: Tomáš Babický
 * Date: 29.08.2020
 * Time: 2:12
 */

namespace Rendix2\FamilyTree\App\Presenters;

use Nette\Application\UI\Form;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Forms\AddressPersonForm;
use Rendix2\FamilyTree\App\Managers\AddressManager;
use Rendix2\FamilyTree\App\Managers\People2AddressManager;
use Rendix2\FamilyTree\App\Managers\PeopleManager;
use Translator;

/**
 * Class AddressPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class AddressPresenter extends BasePresenter
{
    use CrudPresenter;

    /**
     * @var AddressManager $manager
     */
    private $manager;

    /**
     * @var People2AddressManager $people2AddressManager
     */
    private $people2AddressManager;

    /**
     * @var PeopleManager $peopleManager
     */
    private $peopleManager;

    /**
     * AddressPresenter constructor.
     *
     * @param AddressManager $manager
     * @param People2AddressManager $person2AddressManager
     * @param PeopleManager $personManager
     */
    public function __construct(
        AddressManager $manager,
        People2AddressManager $person2AddressManager,
        PeopleManager $personManager
    ) {
        parent::__construct();

        $this->manager = $manager;
        $this->people2AddressManager = $person2AddressManager;
        $this->peopleManager = $personManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $addresses = $this->manager->getAll();

        $this->template->addresses = $addresses;
    }

    /**
     * @param int|null $id
     *
     * @return void
     */
    public function renderEdit($id = null)
    {
        $peoples = $this->people2AddressManager->getFluentByRightJoined($id)->fetchAll();

        $this->template->peoples = $peoples;
    }

    /**
     * @return Form
     */
    protected function createComponentForm()
    {
        $form = new Form();

        $form->setTranslator($this->getTranslator());

        $form->addProtection();
        $form->addText('street', 'address_street');
        $form->addText('streetNumber', 'address_street_number');
        $form->addText('houseNumber', 'address_house_number');

        $form->addText('zip', 'address_zip')
            ->setRequired('address_zip_is_required');

        $form->addText('town', 'address_town')
            ->setRequired('address_town_is_required');

        $form->addSubmit('send', 'save');

        $form->onSuccess[] = [$this, 'saveForm'];
        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }

    /**
     * @return AddressPersonForm
     */
    public function createComponentPeoplesForm()
    {
        return new AddressPersonForm(
            $this->getTranslator(),
            $this->peopleManager,
            $this->people2AddressManager,
            $this->manager
        );
    }
}
