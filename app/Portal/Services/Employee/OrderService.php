<?php

declare(strict_types=1);

namespace App\Portal\Services\Employee;

use App\Helpers\StorageHelper;
use App\Portal\Helpers\AuthHelper;
use App\Portal\Helpers\SettingHelper;
use App\Portal\Models\Order;
use App\Portal\Models\Role;
use App\Portal\Notifications\LeasingBudget\LeasingBudgetLow;
use App\Portal\Repositories\Supplier\OrderRepository;
use App\Portal\Repositories\UserRepository;
use App\System\Repositories\UserRepository as SystemUserRepository;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use PDF;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class OrderService
 *
 * @package App\Portal\Services\Employee
 */
class OrderService
{
    /** @var UserRepository */
    private $userRepository;

    /**
     * ProductService constructor.
     *
     * @param OrderRepository $orderRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
}
