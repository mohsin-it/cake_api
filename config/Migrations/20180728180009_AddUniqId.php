<?php
use Migrations\AbstractMigration;

class AddUniqId extends AbstractMigration
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
        $table = $this->table('profiles');
        $table->addColumn('uniq_id', 'string', ['limit' => 200,'after' => 'id'])
              ->update();
    }
}
