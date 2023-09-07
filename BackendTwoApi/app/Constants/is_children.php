<?php
namespace App\Constants;
class Is_children {
    const TRUE =1;
    const FALSE =0;


    public static function Is_childrenChar()
    {
        return[
            static::TRUE => true,
            static::FALSE => false,
        ];
    }

}
