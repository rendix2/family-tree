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

/**
 * Class BasePresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class BasePresenter extends Presenter
{
    /**
     * @var string
     */
    const FLASH_SUCCESS = 'success';

    /**
     * @var string
     */
    const FLASH_DANGER = 'danger';

    /**
     * @var ITranslator $translator
     * @inject
     */
    public $translator;

    /**
     * @return void
     */
    public function beforeRender()
    {
        parent::beforeRender();

        $this->template->setTranslator($this->translator);
    }
}
