<?php
/**
 *
 * Created by PhpStorm.
 * Filename: BasePresenter.php
 * User: Tomáš Babický
 * Date: 29.08.2020
 * Time: 22:24
 */

namespace Rendix2\FamilyTree\App\Presenters;

use Nette\Application\UI\Presenter;
use Nette\Localization\ITranslator;
use Translator;

/**
 * Class BasePresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class BasePresenter extends Presenter
{
    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * BasePresenter constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->translator = new Translator('cs.CZ');
    }

    /**
     * @return void
     */
    public function beforeRender()
    {
        parent::beforeRender();

        $this->template->setTranslator($this->translator);
    }

    /**
     * @return ITranslator
     */
    public function getTranslator()
    {
        return $this->translator;
    }
}
