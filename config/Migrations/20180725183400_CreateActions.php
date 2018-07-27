<?php
use Migrations\AbstractMigration;

class CreateActions extends AbstractMigration
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
        $table = $this->table('actions');
        $table->addColumn('action', 'string', [
            'default' => null,
            'limit' => 100,
            'null' => false,
        ]);
        $table->addColumn('is_active', 'boolean', [
            'default' => 1,
            'null' => false,
        ]);
        $table->create();
    }
}
