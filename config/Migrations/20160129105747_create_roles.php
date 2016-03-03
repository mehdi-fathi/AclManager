<?php
use Phinx\Migration\AbstractMigration;

class CreateRoles extends AbstractMigration
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
        $table = $this->table('roles');
        $table->addColumn('role', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ])->addColumn('alias', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->create();
    }
}
