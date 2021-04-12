<?php
/**
 *
 * Created by PhpStorm.
 * Filename: Person2JobFacadeSelectRepository.php
 * User: Tomáš Babický
 * Date: 12.04.2021
 * Time: 0:58
 */

namespace Rendix2\FamilyTree\App\Model\Facades\Person2Job;

use Rendix2\FamilyTree\App\Model\Interfaces\ISelectRepository;

/**
 * Class Person2JobFacadeSelectRepository
 *
 * @package Rendix2\FamilyTree\App\Model\Facades\Person2Job
 */
class Person2JobFacadeSelectRepository implements ISelectRepository
{
    /**
     * @var Person2JobFacadeCachedSelector $person2JobFacadeCachedSelector
     */
    private $person2JobFacadeCachedSelector;

    /**
     * @var Person2JobFacadeSelector $person2JobFacadeSelector
     */
    private $person2JobFacadeSelector;

    /**
     * Person2JobFacadeSelectRepository constructor.
     *
     * @param Person2JobFacadeCachedSelector $person2JobFacadeCachedSelector
     * @param Person2JobFacadeSelector       $person2JobFacadeSelector
     */
    public function __construct(
        Person2JobFacadeCachedSelector $person2JobFacadeCachedSelector,
        Person2JobFacadeSelector $person2JobFacadeSelector
    )
    {
        $this->person2JobFacadeCachedSelector = $person2JobFacadeCachedSelector;
        $this->person2JobFacadeSelector = $person2JobFacadeSelector;
    }

    /**
     * @return Person2JobFacadeSelector
     */
    public function getManager()
    {
        return $this->person2JobFacadeSelector;
    }

    /**
     * @return Person2JobFacadeCachedSelector
     */
    public function getCachedManager()
    {
        return $this->person2JobFacadeCachedSelector;
    }
}