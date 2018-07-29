<?php
use Migrations\AbstractMigration;

class ChangeNeutered extends AbstractMigration
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
        $table->changeColumn('neutered', 'boolean')
            ->update();
        $table->changeColumn('intact', 'boolean')
            ->update();
        $table->addColumn('pet_name', 'string', ['limit' => 200, 'after' => 'uniq_id'])
            ->update();
    }
}
