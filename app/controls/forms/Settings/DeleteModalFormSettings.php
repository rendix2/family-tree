<?php
/**
 *
 * Created by PhpStorm.
 * Filename: DeleteModalSetttings.php
 * User: Tomáš Babický
 * Date: 30.03.2021
 * Time: 11:57
 */

namespace Rendix2\FamilyTree\App\Controls\Forms\Settings;

/**
 * Class DeleteModalSettings
 *
 * @package Rendix2\FamilyTree\App\Controls\Forms\Settings
 */
class DeleteModalFormSettings
{
    /**
     * @var callable $callBack
     */
    public $callBack ;

    /**
     * @var bool $httpRedirect
     */
    public $httpRedirect;

    /**
     * DeleteModalSettings constructor.
     */
    public function __construct()
    {
        $this->httpRedirect = false;
    }
}
