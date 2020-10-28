<?php

namespace App\System\Exports;

use App\Http\Requests\DefaultListRequest;
use App\System\Repositories\UserRepository;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * Class UsersExport
 *
 * @package App\System\Exports
 */
class UsersExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /** @var UserRepository */
    public $userRepository;
    /** @var DefaultListRequest */
    public $request;

    /**
     * UsersExport constructor.
     *
     * @param UserRepository     $userRepository
     * @param DefaultListRequest $request
     */
    public function __construct(UserRepository $userRepository, DefaultListRequest $request)
    {
        $this->userRepository = $userRepository;
        $this->request = $request;
    }

    /**
     * @return \Illuminate\Support\Collection
     * @throws \Exception
     */
    public function collection()
    {
        return $this->userRepository->exportList($this->request->validated());
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            __('user.User-ID'),
            __('user.First/Last Name'),
            __('user.Email'),
            __('user.Company'),
            __('user.User Role')
        ];
    }
}
