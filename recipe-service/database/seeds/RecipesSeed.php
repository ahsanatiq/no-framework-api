<?php


use Phinx\Seed\AbstractSeed;

class RecipesSeed extends AbstractSeed
{
    public function run()
    {
        $faker = Faker\Factory::create();
        $data  = [];
        foreach (range(1, 30) as $index) {
            $data[] = [
                'name'        => $faker->word,
                'description' => $faker->paragraphs(3, true),
                'prep_time'   => $faker->numberBetween(10, 60),
                'difficulty'  => $faker->numberBetween(1, 3),
                'vegetarian'  => $faker->randomElement([true, false]),
                'rating'      => $faker->numberBetween(1, 5),
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ];
        }

        $this->insert('recipes', $data);
    }
}
