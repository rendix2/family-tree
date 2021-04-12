<?php
/**
 *
 * Created by PhpStorm.
 * Filename: IPersonSelectRepository.php
 * User: Tomáš Babický
 * Date: 03.04.2021
 * Time: 2:06
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Person\Interfaces;


use Rendix2\FamilyTree\App\Model\Interfaces\ISelectRepository;

interface IPersonSelectRepository extends ISelectRepository
{
    public function getSettingsManager();

    public function getSettingsCachedManager();
}
