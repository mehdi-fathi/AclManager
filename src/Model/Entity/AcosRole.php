<?php
namespace AclManager\Model\Entity;

use Cake\ORM\Entity;

/**
 * AcosRole Entity.
 */
class AcosRole extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'aco_id' => true,
        'role_id' => true,
        '_create' => true,
        '_read' => true,
        '_update' => true,
        '_delete' => true,
        'aco' => true,
        'role' => true,
    ];
}
