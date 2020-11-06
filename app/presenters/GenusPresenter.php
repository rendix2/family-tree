<?php
/**
 *
 * Created by PhpStorm.
 * Filename: GenusPresenter.php
 * User: Tomáš Babický
 * Date: 29.08.2020
 * Time: 22:34
 */

namespace Rendix2\FamilyTree\App\Presenters;

use Nette\Application\UI\Form;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Filters\GenusFilter;
use Rendix2\FamilyTree\App\Filters\NameFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Managers\GenusManager;
use Rendix2\FamilyTree\App\Managers\NameManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Presenters\Traits\Genus\GenusPersonNameDeleteModal;

/**
 * Class GenusPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class GenusPresenter extends BasePresenter
{
    use CrudPresenter;

    use GenusPersonNameDeleteModal;

    /**
     * @var GenusManager $manager
     */
    private $manager;

    /**
     * @var NameManager $nameManager
     */
    private $nameManager;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * GenusPresenter constructor.
     *
     * @param GenusManager $manager
     * @param NameManager $nameManager
     * @param PersonManager $personManager
     */
    public function __construct(
        GenusManager $manager,
        NameManager $nameManager,
        PersonManager $personManager
    ) {
        parent::__construct();

        $this->manager = $manager;
        $this->nameManager = $nameManager;
        $this->personManager = $personManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $genuses = $this->manager->getAll();

        $this->template->genuses = $genuses;
    }

    /**
     * @param int|null $id
     */
    public function renderEdit($id = null)
    {
        if ($id === null) {
            $genusPersons = [];
            $genusNamePersons = [];
        } else {
            $genusPersons = $this->personManager->getByGenusId($id);
            $genusNamePersons = $this->nameManager->getByGenusId($id);

            foreach ($genusNamePersons as $genusNamePerson) {
                $person = $this->personManager->getByPrimaryKey($genusNamePerson->personId);

                $genusNamePerson->person = $person;
            }
        }

        $this->template->genusPersons = $genusPersons;
        $this->template->genusNamePersons = $genusNamePersons;
        $this->template->genus = $this->item;

        $this->template->addFilter('genus', new GenusFilter());
        $this->template->addFilter('name', new NameFilter());
        $this->template->addFilter('person', new PersonFilter($this->getTranslator()));
    }

    /**
     * @return Form
     */
    public function createComponentForm()
    {
        $form = new Form();

        $form->setTranslator($this->getTranslator());

        $form->addProtection();

        $form->addText('surname', 'genus_surname')
            ->setRequired('genus_surname_required');

        $form->addText('surnameFonetic', 'genus_surname_fonetic')
            ->setNullable();

        $form->addSubmit('send', 'save');

        $form->onSuccess[] = [$this, 'saveForm'];
        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }
}
