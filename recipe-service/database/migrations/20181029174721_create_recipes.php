<?php


use Phinx\Migration\AbstractMigration;

class CreateRecipes extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('recipes');
        $table
        ->addColumn('name', 'string', ['limit' => 100])
        ->addColumn('description', 'text')
        ->addColumn('prep_time', 'integer', ['limit' => 4])
        ->addColumn('difficulty', 'integer', ['limit' => 1])
        ->addColumn('vegetarian', 'boolean', ['default' => false, 'null' => true])
        ->addColumn('created_at', 'datetime', ['null' => true])
        ->addColumn('updated_at', 'datetime', ['null' => true])
        ->create();
    }
}
