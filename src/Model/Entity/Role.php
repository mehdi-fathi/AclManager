<?php
namespace AclManager\Model\Entity;

use Cake\ORM\Entity;

/**
 * Role Entity.
 */
class Role extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'role' => true,
        'alias' => true,
    ];
}
