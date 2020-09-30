<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PlacePresenter.php
 * User: Tomáš Babický
 * Date: 20.09.2020
 * Time: 0:11
 */

namespace Rendix2\FamilyTree\App\Presenters;

use Nette\Application\UI\Form;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Filters\PlaceFilter;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\PlaceManager;
use Rendix2\FamilyTree\App\Managers\WeddingManager;

/**
 * Class PlacePresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class PlacePresenter extends BasePresenter
{
    use CrudPresenter;

    /**
     * @var PlaceManager $manager
     */
    private $manager;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var WeddingManager $weddingManager
     */
    private $weddingManager;

    /**
     * PlacePresenter constructor.
     *
     * @param PersonManager $personManager
     * @param PlaceManager $placeManager
     * @param WeddingManager $weddingManager
     */
    public function __construct(
        PersonManager $personManager,
        PlaceManager $placeManager,
        WeddingManager $weddingManager
    ) {
        parent::__construct();

        $this->manager = $placeManager;
        $this->personManager = $personManager;
        $this->weddingManager = $weddingManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $places = $this->manager->getAll();

        $this->template->places = $places;
    }

    /**
     * @param int|null $id
     */
    public function renderEdit($id = null)
    {
        if ($id === null) {
            $birthPersons = [];
            $deathPersons = [];
            $weddings = [];
        } else {
            $birthPersons = $this->personManager->getByBirthPlaceId($id);
            $deathPersons = $this->personManager->getByDeathPlaceId($id);
            $weddings = $this->weddingManager->getByPlaceId($id);

            foreach ($weddings as $wedding) {
                $husband = $this->personManager->getByPrimaryKey($wedding->husbandId);
                $wife = $this->personManager->getByPrimaryKey($wedding->wifeId);

                $wedding->husband = $husband;
                $wedding->wife = $wife;
            }
        }

        $this->template->birthPersons = $birthPersons;
        $this->template->deathPersons = $deathPersons;
        $this->template->place = $this->item;
        $this->template->weddings = $weddings;

        $this->template->addFilter('person', new PersonFilter($this->getTranslator()));
        $this->template->addFilter('place', new PlaceFilter());
    }

    /**
     * @return Form
     */
    public function createComponentForm()
    {
        $form = new Form();

        $form->setTranslator($this->getTranslator());

        $form->addProtection();

        $form->addText('name', 'place_name')
            ->setRequired('place_name_required');

        $form->addSubmit('send', 'save');

        $form->onSuccess[] = [$this, 'saveForm'];
        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }
}
