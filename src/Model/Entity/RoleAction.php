<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * RoleAction Entity
 *
 * @property int $id
 * @property int $role_id
 * @property int $action_id
 * @property bool $is_allowed
 *
 * @property \App\Model\Entity\Role $role
 * @property \App\Model\Entity\Action $action
 */
class RoleAction extends Entity
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
        'role_id' => true,
        'action_id' => true,
        'is_allowed' => true,
        'role' => true,
        'action' => true
    ];
}
