<?php

namespace App\Helpers;

use App\Models\Phone;
use Illuminate\Http\Request;

class PhoneHelper
{
    public static function save(Request $request, $user)
    {
        $phones = $request->input('outer-group');

        if (!empty($phones) && count($phones) > 0) {
            foreach ($phones as $phone) {
                $phone_number = data_get($phone, 'phone');
                if(!empty($phone_number)) {
                    $user->phones()->updateOrCreate([
                        'phoneable_id' => data_get($user, 'id'),
                        'phone' => $phone_number
                    ], [
                        'type' => (int) data_get($phone, 'type')
                    ]);
                }
            }
        }
    }

    public static function delete($id): bool
    {
        $phone = Phone::find($id);

        if(!empty($phone)) {
            $phone->delete();
            return true;
        }

        return false;
    }
}
