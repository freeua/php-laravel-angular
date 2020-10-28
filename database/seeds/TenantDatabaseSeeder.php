<?php

use App\Models\ProductCategory;
use App\Portal\Models\Role;
use Illuminate\Database\Seeder;

/**
 * Class TenantDatabaseSeeder
 */
class TenantDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->addRolesAndPermissions();
        $this->addProductCategoriesCities();
        $this->addCities();
    }

    private function addRolesAndPermissions()
    {
        Role::create(['name' => 'Portal Admin', 'guard_name' => Role::GUARD_API]);
        Role::create(['name' => 'Company Admin', 'guard_name' => Role::GUARD_API]);
        Role::create(['name' => 'Supplier Admin', 'guard_name' => Role::GUARD_API]);
        Role::create(['name' => 'Employee', 'guard_name' => Role::GUARD_API]);
    }

    private function addCities()
    {
        $file = resource_path('data/cities.json');

        if (!file_exists($file)) {
            $this->command->error('Cities json file did\'n found');

            return;
        }

        $data = json_decode(file_get_contents($file));

        foreach ($data as $item) {
            DB::table('cities')->insert([
                'name' => $item->city,
                'lat'  => $item->lat,
                'lng'  => $item->lng
            ]);
        }
    }

    private function addProductCategoriesCities()
    {
        if (ProductCategory::all()->count()) {
            return;
        }

        $categories = [
            ['name' => 'Fahrrad', 'service_rate' => 11.50],
            ['name' => 'Pedelac', 'service_rate' => 14.00],
            ['name' => 'S-Pedelac', 'service_rate' => 17.60]
        ];

        foreach ($categories as $id => $category) {
            ProductCategory::create($category);
        }
    }
}
