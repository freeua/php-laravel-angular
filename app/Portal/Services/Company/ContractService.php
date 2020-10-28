<?php

namespace App\Portal\Services\Company;

use App\Exports\ContractsExport;
use App\Portal\Notifications\Contract\ContractsExported;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Notification;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use App\Portal\Helpers\AuthHelper;
use App\Portal\Repositories\Supplier\UserRepository;
use App\Portal\Models\Contract;
use App\System\Repositories\ContractRepository;
use Carbon\Carbon;

/**
 * Class ContractService
 *
 * @package App\Portal\Services
 */
class ContractService
{
    /** @var UserRepository */
    private $userRepository;
    /** @var ContractRepository */
    private $contractRepository;

    /**
     * ContractService constructor.
     *
     * @param ContractRepository $contractRepository
     * @param UserRepository     $userRepository
     */
    public function __construct(
        ContractRepository $contractRepository,
        UserRepository $userRepository
    ) {
        $this->contractRepository = $contractRepository;
        $this->userRepository = $userRepository;
    }

    /**
     *
     * @param $target
     * @return Response
     */
    public function generatePDFExport($target)
    {
        $user       = AuthHelper::user();
        $company    = AuthHelper::companyId();
        $fileName   = 'vertrage_exportiert_' . Carbon::now()->format('dmY_His') . '.pdf';

        $data['user'] = $user;
        $data['contracts'] = Contract::where('company_id', $company)
            ->orderBy('created_at', 'desc')
            ->get();

        $pdf = PDF::loadView('portal.contract.export', $data);

        if ($target === 'email') {
            Notification::send($user, new ContractsExported($pdf->output('', 'S'), $fileName, null));
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
        $fileName   = 'vertrage_exportiert_' . Carbon::now()->format('dmY_His') . '.xlsx';

        $data['user'] = $user;
        $data['contracts'] = Contract::where('company_id', $company)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($target === 'email') {
            $path = Excel::download(new ContractsExport(), $fileName)->getFile();
            Notification::send($user, new ContractsExported(null, $fileName, $path));
            return response()->success();
        } elseif ($target === 'download') {
            return Excel::download(new ContractsExport(), $fileName);
        } else {
            return response()->error('Unknown target type');
        }
    }
}
