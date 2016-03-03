<?php
namespace AclManager\Model\Entity;

use Cake\ORM\Entity;

/**
 * User Entity.
 */
class User extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'name' => true,
        'username' => true,
        'password' => true,
        'role_id' => true,
        'lastlogin' => true,
        'image' => true,
        'posts' => true,
        'reply_comment' => true,
    ];
}
