<?php

declare(strict_types=1);

namespace App\Portal\Services\Company;

use App\Exports\OrdersExport;
use App\Portal\Helpers\AuthHelper;
use App\Portal\Models\Order;
use App\Portal\Notifications\Order\OrdersExported;
use Carbon\Carbon;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Notification;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

/**
 * Class Orderservice
 *
 * @package App\Portal\Services\Employee
 */
class OrderService
{
    /**
     *
     * @param $target
     * @return Response
     */
    public function generatePDFExport($target)
    {
        $user       = AuthHelper::user();
        $company    = AuthHelper::companyId();
        $fileName   = 'bestellungen_exportiert_' . Carbon::now()->format('dmY_His') . '.pdf';

        $data['user'] = $user;
        $data['orders'] = Order::where('company_id', $company)
            ->orderBy('created_at', 'desc')
            ->get();

        $pdf = PDF::loadView('portal.order.export', $data);

        if ($target === 'email') {
            Notification::send($user, new OrdersExported($pdf->output('', 'S'), $fileName, null));
            return response()->success();
        } elseif ($target === 'download') {
            $pdf = $pdf->output();
            $response = response($pdf, 200, [
                'Content-Type' => 'application/pdf',
                'Content-disposition' => 'inline; filename="' . $fileName . '"',
                'Cache-Control' => ' public, must-revalidate, max-age=0',
                'Pragma' => 'public',
                'X-Generator' => 'mPDF ' . \Mpdf\Mpdf::VERSION,
                'Expires' => 'Sat, 26 Jul 1997 05:00:00 GMT',
                'Last-Modified' => gmdate('D, d M Y H:i:s') . ' GMT',
            ]);
            return $response;
        } else {
            return response()->error('Unknown target type');
        }
    }

    /**
     *
     * @param $target
     * @return Response
     */
    public function generateExcelExport($target)
    {
        $user       = AuthHelper::user();
        $company    = AuthHelper::companyId();
        $fileName   = 'bestellungen_exportiert_' . Carbon::now()->format('dmY_His') . '.xlsx';

        $data['user'] = $user;
        $data['orders'] = Order::where('company_id', $company)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($target === 'email') {
            $path = Excel::download(new OrdersExport(), $fileName)->getFile();
            Notification::send($user, new OrdersExported(null, $fileName, $path));
            return response()->success();
        } elseif ($target === 'download') {
            return Excel::download(new OrdersExport(), $fileName);
        } else {
            return response()->error('Unknown target type');
        }
    }
}
