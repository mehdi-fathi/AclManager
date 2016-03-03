<?php
use Phinx\Migration\AbstractMigration;

class CreateAcos extends AbstractMigration
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
        $table = $this->table('acos');
        $table
              ->addColumn('parent_id', 'integer',['default' => null,'limit' => 11])
              ->addColumn('alias', 'string',['limit' => 255])
              ->addColumn('lft', 'integer',['limit' => 11])
              ->addColumn('rght', 'integer',['limit' => 1]);
              
              $table->create();
    }
}
