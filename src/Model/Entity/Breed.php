<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Breed Entity
 *
 * @property int $id
 * @property int $pet_id
 * @property string $name
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Pet $pet
 * @property \App\Model\Entity\Profile[] $profiles
 */
class Breed extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'pet_id' => true,
        'name' => true,
        'created' => true,
        'modified' => true,
        'pet' => true,
        'profiles' => true
    ];
}
