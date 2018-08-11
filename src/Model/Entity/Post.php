<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Post Entity
 *
 * @property int $id
 * @property string $media
 * @property string $caption
 * @property int $pet_id
 * @property int $user_id
 * @property int $status
 * @property bool $is_active
 * @property string $host
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Pet $pet
 * @property \App\Model\Entity\User $user
 */
class Post extends Entity
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
        'media' => true,
        'caption' => true,
        'pet_id' => true,
        'user_id' => true,
        'status' => true,
        'is_active' => true,
        'host' => true,
        'created' => true,
        'modified' => true,
        'pet' => true,
        'user' => true
    ];
}
