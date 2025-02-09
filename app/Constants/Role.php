<?php

namespace App\Constants;


final class Role
{
    public const CUSTOMER = 'customer';
    public const SALE_MANAGER = 'sale';
    public const DRIVER = 'driver';
    public const WAREHOUSE_MANAGER = 'warehouse';

    public static function getAllRoles()
    {
        return [
            self::CUSTOMER,
            self::SALE_MANAGER,
            self::DRIVER,
            self::WAREHOUSE_MANAGER,
        ];
    }
}
