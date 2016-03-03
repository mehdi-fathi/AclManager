<?php
namespace AclManager\Model\Entity;

use Cake\ORM\Entity;

/**
 * Aco Entity.
 */
class Aco extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'parent_id' => true,
        'model' => true,
        'foreign_key' => true,
        'alias' => true,
        'lft' => true,
        'rght' => true,
        'parent_aco' => true,
        'child_acos' => true,
        'roles' => true,
    ];
}
