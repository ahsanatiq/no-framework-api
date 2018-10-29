<?php


use Phinx\Seed\AbstractSeed;

class RatingsSeed extends AbstractSeed
{
    public function run()
    {
       $faker = Faker\Factory::create();
       $rowsRecipes = $this->fetchAll('SELECT * FROM recipes');
       foreach($rowsRecipes as $rowRecipe)
       {
            foreach (range(1, 30) as $index) {
                $data[] = [
                    'rating'      => $faker->numberBetween(1, 5),
                    'recipe_id'   => $rowRecipe['id'],
                    'created_at'  => date('Y-m-d H:i:s'),
                    'updated_at'  => date('Y-m-d H:i:s'),
                ];
            }

            $this->insert('ratings', $data);
       }
   }
}
