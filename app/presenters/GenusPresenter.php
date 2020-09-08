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
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Managers\GenusManager;

/**
 * Class GenusPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class GenusPresenter extends BasePresenter
{
    use CrudPresenter;

    /**
     * @var GenusManager $manager
     */
    private $manager;

    /**
     * GenusPresenter constructor.
     *
     * @param GenusManager $manager
     */
    public function __construct(GenusManager $manager)
    {
        parent::__construct();

        $this->manager = $manager;
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
     * @return Form
     */
    public function createComponentForm()
    {
        $form = new Form();

        $form->setTranslator($this->getTranslator());

        $form->addProtection();

        $form->addText('surname', 'genus_surname')
            ->setRequired('genus_surname_is_required');

        $form->addSubmit('send', 'save');

        $form->onSuccess[] = [$this, 'saveForm'];
        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }
}
