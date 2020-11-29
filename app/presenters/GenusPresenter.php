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
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Filters\DurationFilter;
use Rendix2\FamilyTree\App\Filters\GenusFilter;
use Rendix2\FamilyTree\App\Filters\NameFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\GenusForm;
use Rendix2\FamilyTree\App\Managers\GenusManager;
use Rendix2\FamilyTree\App\Managers\NameManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Model\Facades\GenusFacade;
use Rendix2\FamilyTree\App\Model\Facades\NameFacade;
use Rendix2\FamilyTree\App\Presenters\Traits\Genus\GenusEditDeleteModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Genus\GenusListDeleteModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Genus\GenusPersonGenusDeleteModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Genus\GenusPersonNameDeleteModal;

/**
 * Class GenusPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class GenusPresenter extends BasePresenter
{

    use GenusListDeleteModal;
    use GenusEditDeleteModal;

    use GenusPersonNameDeleteModal;
    use GenusPersonGenusDeleteModal;

    /**
     * @var GenusManager $genusManager
     */
    private $genusManager;

    /**
     * @var NameFacade $nameFacade
     */
    private $nameFacade;

    /**
     * @var NameManager $nameManager
     */
    private $nameManager;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * GenusPresenter constructor.
     *
     * @param GenusManager $manager
     * @param NameFacade $nameFacade
     * @param NameManager $nameManager
     * @param PersonFacade $personFacade
     */
    public function __construct(
        GenusManager $manager,
        NameFacade $nameFacade,
        NameManager $nameManager,
        PersonManager $personManager,
        PersonFacade $personFacade
    ) {
        parent::__construct();

        $this->genusManager = $manager;
        $this->nameFacade = $nameFacade;
        $this->nameManager = $nameManager;
        $this->personManager = $personManager;
        $this->personFacade = $personFacade;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $genuses = $this->genusManager->getAllCached();

        $this->template->genuses = $genuses;

        $this->template->addFilter('genus', new GenusFilter());
    }

    /**
     * @param int|null $id
     */
    public function actionEdit($id = null)
    {
        if ($id !== null) {
            $genus = $this->genusManager->getByPrimaryKey($id);

            if (!$genus) {
                $this->error('Item not found.');
            }

            $this['form']->setDefaults((array)$genus);
        }
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
            $genusPersons = $this->personFacade->getByGenusIdCached($id);
            $genusNamePersons = $this->nameFacade->getByGenusIdCached($id);
        }

        $genus = $this->genusManager->getByPrimaryKeyCached($id);

        $this->template->genusPersons = $genusPersons;
        $this->template->genusNamePersons = $genusNamePersons;
        $this->template->genus = $genus;

        $this->template->addFilter('duration', new DurationFilter($this->getTranslator()));
        $this->template->addFilter('genus', new GenusFilter());
        $this->template->addFilter('name', new NameFilter());
        $this->template->addFilter('person', new PersonFilter($this->getTranslator(), $this->getHttpRequest()));
    }

    /**
     * @return Form
     */
    public function createComponentForm()
    {
        $formFactory = new GenusForm($this->getTranslator());

        $form = $formFactory->create();
        $form->onSuccess[] = [$this, 'saveForm'];

        return $form;
    }

    /**getPeronFacade
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveForm(Form $form, ArrayHash $values)
    {
        $id = $this->getParameter('id');

        if ($id) {
            $this->genusManager->updateByPrimaryKey($id, $values);
            $this->flashMessage('item_updated', self::FLASH_SUCCESS);
        } else {
            $id = $this->genusManager->add($values);
            $this->flashMessage('item_added', self::FLASH_SUCCESS);
        }

        $this->redirect('Genus:edit', $id);
    }
}
