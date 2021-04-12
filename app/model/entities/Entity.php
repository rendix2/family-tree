<?php
/**
 *
 * Created by PhpStorm.
 * Filename: Construct.php
 * User: Tomáš Babický
 * Date: 10.11.2020
 * Time: 3:55
 */

namespace Rendix2\FamilyTree\App\Model\Entities;

/**
 * Trait Construct
 *
 * @package Rendix2\FamilyTree\App\Model\Entities
 */
trait Entity
{
    /**
     * Construct constructor.
     *
     * @param array $array
     */
    public function __construct(array $array)
    {
        foreach ($array as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            } else {
                $this->{'_' . $key} = $value;
            }
        }
    }

    /**
     * @return void
     */
    public function clean()
    {
        foreach ($this as $key => $value) {
            if (strpos($key, '_') === 0) {
                unset($this->{$key});
            }
        }
    }
}
