<?php
use Migrations\AbstractMigration;

class AddUserId extends AbstractMigration
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
        $table = $this->table('groups');
        $table->addColumn('user_id', 'integer', ['after' => 'lng','default' => null])
              ->update();
    }
}
