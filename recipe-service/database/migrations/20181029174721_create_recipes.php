<?php
namespace database;

use Phinx\Migration\AbstractMigration;
use Phinx\Util\Literal;

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
        ->addColumn('vegetarian', 'boolean', ['null' => false, 'default' => false])
        ->addColumn('rating', 'float', ['null' => false, 'default' => '0'])
        ->addColumn('created_at', 'datetime', ['null' => false, 'default' => Literal::from('now()')])
        ->addColumn('updated_at', 'datetime', ['null' => false, 'default' => Literal::from('now()')])
        ->addColumn('deleted_at', 'datetime', ['null' => true])
        ->create();
    }
}
