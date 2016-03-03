<?php
use Phinx\Migration\AbstractMigration;

class CreateAcosRoles extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('acos_roles');
        $table->addColumn('aco_id', 'integer', [
            'default' => null,
            'limit' => 10,
            'null' => false,
        ])->addColumn('role_id', 'integer', [
            'default' => null,
            'limit' => 10,
            'null' => false,
        ])->addColumn('_create', 'integer',['default'=>0,'limit' => 1])
        ->addColumn('_read', 'integer',['default'=>0,'limit' => 1])
        ->addColumn('_update', 'integer',['default'=>0,'limit' => 1])
        ->addColumn('_delete', 'integer',['default'=>0,'limit' => 1]);

        $table->create();
    }
}
