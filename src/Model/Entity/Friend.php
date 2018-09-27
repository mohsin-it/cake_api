<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Friend Entity
 *
 * @property int $id
 * @property int $requester_id
 * @property int $receiver_id
 * @property int $actioner_id
 * @property int $status
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Requester $requester
 * @property \App\Model\Entity\Receiver $receiver
 * @property \App\Model\Entity\Actioner $actioner
 */
class Friend extends Entity
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
        'requester_id' => true,
        'receiver_id' => true,
        'actioner_id' => true,
        'status' => true,
        'created' => true,
        'modified' => true,
        'requester' => true,
        'receiver' => true,
        'actioner' => true
    ];
}
