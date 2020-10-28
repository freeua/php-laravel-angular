<?php

namespace App\Console\Commands;

use App\Helpers\StorageHelper;
use App\Documents\Models\Document;
use App\Portal\Models\Order;
use App\Portal\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Storage;

class RegenerateELV extends Command
{
    protected $signature = 'mercator:regenerate-elv {orders}';

    protected $description = 'Regenerates for order or orders a elv';

    /** @var OrderService */
    public $orderService;

    public function __construct(OrderService $orderService)
    {
        parent::__construct();
        $this->orderService = $orderService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $app = require_once __DIR__.'/../../../bootstrap/app.php';
        $this->setLaravel($app);
        $orders = explode(',', $this->argument('orders'));
        foreach ($orders as $orderId) {
            $order = Order::find($orderId);
            \Log::debug("Generating ELV for order " . $order->number);
            $singleLeasePdf = OrderService::generateSingleLeasePdf($order);
            $singleLeaseName = "Einzelleasingvertrag_{$order->number}_{$order->employeeName}_".Carbon::now()->format('Y.m.d');
            $singleLeasePath = "/orders/{$order->number}/{$singleLeaseName}.pdf";
            Storage::disk(StorageHelper::PRIVATE_DISK)->put($singleLeasePath, $singleLeasePdf);

            $order->singleLeasingFile = $singleLeasePath;

            $order->saveOrFail();

            \Log::debug("Generated ELV for order " . $order->number);
        }
    }
}
