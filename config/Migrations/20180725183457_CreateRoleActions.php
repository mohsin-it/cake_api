<?php
use Migrations\AbstractMigration;

class CreateRoleActions extends AbstractMigration
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
        $table = $this->table('role_actions');
        $table->addColumn('role_id', 'integer', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('action_id', 'integer', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('is_allowed', 'boolean', [
            'default' => 0,
            'null' => false,
        ]);
        $table->create();
    }
}
