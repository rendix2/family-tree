<?php
/**
 *
 * Created by PhpStorm.
 * Filename: JobPresenter.php
 * User: Tomáš Babický
 * Date: 09.02.2021
 * Time: 23:20
 */

namespace Rendix2\FamilyTree\SettingsModule\App\Presenters;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class JobPresenter
 *
 * @package Rendix2\FamilyTree\SettingsModule\App\Presenters
 */
class JobPresenter extends BasePresenter
{
    /**
     * JobPresenter constructor.
     *
     */
    public function __construct()
    {
        parent::__construct();

    }

    /**
     * @return void
     */
    public function actionDefault()
    {
    }

    /**
     * @return Form
     */
    protected function createComponentJobForm()
    {
        $form = new Form();

        $form->setTranslator($this->getTranslator());

        $form->addProtection();


        $form->addSubmit('send', 'save');

        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];
        $form->onSuccess[]= [$this, 'jobFormSuccess'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function jobFormSuccess(Form $form, ArrayHash $values)
    {
    }
}
