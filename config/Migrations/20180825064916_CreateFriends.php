<?php
use Migrations\AbstractMigration;

class CreateFriends extends AbstractMigration
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
        $table = $this->table('friends');  
        $table->addColumn('requester_id', 'integer', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('receiver_id', 'integer', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('actioner_id', 'integer', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('status', 'integer', [
            'default' => null,
            'limit' => 2,
            'null' => false,
        ]);
        $table->addColumn('created', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->create();
    }
}
