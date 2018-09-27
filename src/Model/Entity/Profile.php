<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Profile Entity
 *
 * @property int $id
 * @property string $uniq_id
 * @property string $pet_name
 * @property int $owner_id
 * @property int $pet_id
 * @property int $breed_id
 * @property int $size
 * @property bool $gender
 * @property string $age
 * @property string $color
 * @property bool $neutered
 * @property bool $intact
 * @property string $mix
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * 
 * 
 * @property \App\Model\Entity\Pet $pet
 * @property \App\Model\Entity\Breed $breed
 */
class Profile extends Entity
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
        'uniq_id' => true,
        'pet_name' => true,
        'owner_id' => true,
        'pet_id' => true,
        'breed_id' => true,
        'size' => true,
        'gender' => true,
        'age' => true,
        'color' => true,
        'neutered' => true,
        'intact' => true,
        'mix' => true,
        'created' => true,
        'modified' => true,
        'pet' => true,
        'breed' => true
    ];
}
