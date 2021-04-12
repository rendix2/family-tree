<?php
/**
 *
 * Created by PhpStorm.
 * Filename: ISettingsSelectRepository.php
 * User: Tomáš Babický
 * Date: 06.04.2021
 * Time: 15:11
 */

namespace Rendix2\FamilyTree\App\Model\Interfaces;

/**
 * Interface ISettingsSelectRepository
 *
 * @package Rendix2\FamilyTree\App\Model\Interfaces
 */
interface ISettingsSelectRepository extends ISelectRepository
{
    public function getSettingsManager();

    public function getSettingsCachedManager();
}