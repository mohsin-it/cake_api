<?php
use Migrations\AbstractMigration;

class AddIp extends AbstractMigration
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
        $table = $this->table('chats');
        $table->addColumn('ip', 'string', ['limit' => 60,'after' => 'status','default' => null])
              ->update();
    }
}
