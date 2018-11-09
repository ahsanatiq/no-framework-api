<?php
namespace database\migrations;

use Phinx\Migration\AbstractMigration;

class CreateRatings extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('ratings', ['id' => false]);
        $table
        ->addColumn('rating', 'integer', ['limit' => 1])
        ->addColumn('recipe_id', 'integer')
        ->addColumn('created_at', 'datetime', ['null' => true])
        ->addColumn('updated_at', 'datetime', ['null' => true])
        ->addForeignKey('recipe_id', 'recipes', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
        ->create();
    }
}
