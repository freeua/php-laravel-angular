<?php

use Illuminate\Database\Seeder;

if (!function_exists('generateCodeOffer')) {
    function generateCodeOffer(
        string $name,
        int $lettersCount = 3,
        int $digitsCount = 3,
        string $delimiter = '',
        string $prefix = '',
        string $lastId = ''
    ): string {
        $code = $prefix . strtoupper(substr(preg_replace('/\W/', '', $name), 0, $lettersCount)) . $delimiter;
        $code .= str_pad(++$lastId, $digitsCount, '0', STR_PAD_LEFT);

        return $code;
    }
}

class OfferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $portal = app(\App\Models\Portal::class)->newQuery()->first();
        $suppliers = app(\App\Portal\Models\Supplier::class)->newQuery()->get();
        $products = app(\App\Portal\Models\Product::class)->newQuery()->get();
        $userEmployees = app(\App\Portal\Models\User::class)->newQuery()
            ->whereNotNull('company_id')
            ->join('roles', 'roles.id', '=', 'portal_users.id')->get();
        $userSuppliers = app(\App\Portal\Models\User::class)->newQuery()
            ->whereNotNull('supplier_id')->get();
        $statuses = app(\App\Models\Status::class)->newQuery()->where('table', '=', 'offers')->get();

        factory(\App\Portal\Models\Offer::class, 100)->make()
        ->each(function(\App\Portal\Models\Offer $offer)
                use ($suppliers, $products, $userEmployees, $userSuppliers, $statuses, $portal) {
            $user = $userEmployees->random();
            $offer->fill([
                'portal_id'         => $portal->id,
                'company_id'        => $user->company_id,
                'product_id'        => $products->random()->id,
                'user_id'           => $user->id,
                'supplier_user_id'  => $userSuppliers->random()->id,
                'status_id'         => $statuses->random()->id,
            ]);
            $offer->save();
        });
    }
}
