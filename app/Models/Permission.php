<?php
/**
 * Created by PhpStorm.
 * User: jpicornell
 * Date: 13/10/2018
 * Time: 11:28
 */

namespace App\Models;

use Spatie\Permission\Models\Permission as BasePermission;

/**
 * Class Permission
 * @package App\Models
 * @property int     id
 * @property string     label
 * @property string      name
 * @property string      guard_name
 */
class Permission extends BasePermission
{
    const MANAGE_COMPANY_EMPLOYEES = 'Manage Company Employees';
    const READ_COMPANY_DATA = 'Read Company Data';
    const EDIT_COMPANY_DATA = 'Edit Company Data';
    const EDIT_PORTAL_DATA = 'Edit Portal Data';
}
