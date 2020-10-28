<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\System\Models\User as SystemUser;
use App\Portal\Notifications\CustomNotification;

class Notification extends Model
{
    const EMAIL = 1;
    const NOTIFICATION = 2;

    const SYSTEM = 'system';
    const PORTAL = 'portal';
    const SUPPLIER = 'supplier';
    const COMPANY = 'company';

    public $incrementing = false;
    protected $table = 'notifications';
    protected $casts = [
        'data' => 'array'
    ];

    public function type()
    {
        if (strpos($this->type, CustomNotification::class) !== false) {
            return self::NOTIFICATION;
        }
        return self::EMAIL;
    }

    public function sender()
    {
        if (empty($this->data['sender_id'])) {
            return null;
        }
        $senderId = $this->data['sender_id'];
        $senderType = $this->data['sender_type'];
        $user = resolve($senderType);
        $sender = $user->find($senderId);
        $sender->is_system = $sender->getRoleName() === SystemUser::ROLE_ADMIN;
        return $sender;
    }

    public function getStatus()
    {
        $status = 'Nicht gelesen';
        if ($this->read_at) {
            $status = 'Lesen';
        }
        return $status;
    }
    
    public function notifiable()
    {
        return $this->morphTo();
    }
}
