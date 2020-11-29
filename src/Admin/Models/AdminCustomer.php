<?php

namespace SCart\Core\Admin\Models;

use SCart\Core\Front\Models\ShopCustomer;
use SCart\Core\Front\Models\ShopCustomerAddress;

class AdminCustomer extends ShopCustomer
{
    protected static $getListTitleAdmin = null;
    protected static $getListCustomerGroupByParentAdmin = null;
    private static $getList = null;
    /**
     * Get customer detail in admin
     *
     * @param   [type]  $id  [$id description]
     *
     * @return  [type]       [return description]
     */
    public static function getCustomerAdmin($id) {
        return self::with('addresses')
        ->where('id', $id)
        ->where('store_id', session('adminStoreId'))
        ->first();
    }

    /**
     * Get customer detail in admin json
     *
     * @param   [type]  $id  [$id description]
     *
     * @return  [type]       [return description]
     */
    public static function getCustomerAdminJson($id) {
        return self::getCustomerAdmin($id)
        ->toJson();
    }

    /**
     * Get list customer in admin
     *
     * @param   [array]  $dataSearch  [$dataSearch description]
     *
     * @return  [type]               [return description]
     */
    public static function getCustomerListAdmin(array $dataSearch) {
        $keyword          = $dataSearch['keyword'] ?? '';
        $sort_order       = $dataSearch['sort_order'] ?? '';
        $arrSort          = $dataSearch['arrSort'] ?? '';

        $customerList = (new ShopCustomer)
            ->where('store_id', session('adminStoreId'));

        if ($keyword) {
            $customerList->where('email', 'like', '%' . $keyword . '%')
            ->orWhere('last_name', 'like', '%' . $keyword . '%')
            ->orWhere('first_name', 'like', '%' . $keyword . '%');
        }

        if ($sort_order && array_key_exists($sort_order, $arrSort)) {
            $field = explode('__', $sort_order)[0];
            $sort_field = explode('__', $sort_order)[1];
            $customerList = $customerList->orderBy($field, $sort_field);
        } else {
            $customerList = $customerList->orderBy('id', 'desc');
        }
        $customerList = $customerList->paginate(20);

        return $customerList;
    }

    /**
     * Find address id
     *
     * @param   [type]  $id  [$id description]
     *
     * @return  [type]       [return description]
     */
    public static function getAddress($id) {
        return ShopCustomerAddress::find($id);
    }

    /**
     * Delete address id
     *
     * @return  [type]  [return description]
     */
    public static function deleteAddress($id) {
        return ShopCustomerAddress::where('id', $id)->delete();
    }

    /**
     * Get total customer of store
     *
     * @return  [type]  [return description]
     */
    public static function getTotalCustomerStore() {
        return self::where('store_id', session('adminStoreId'))->count();
    }

    /**
     * Get total customer of system
     *
     * @return  [type]  [return description]
     */
    public static function getTotalCustomer() {
        return self::count();
    }


    /**
     * Get total customer of store
     *
     * @return  [type]  [return description]
     */
    public static function getTopCustomerStore() {
        return self::where('store_id', session('adminStoreId'))
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();
    }


    /**
     * Get total customer of system
     *
     * @return  [type]  [return description]
     */
    public static function getTopCustomer() {
        return self::orderBy('id', 'desc')
            ->limit(10)
            ->get();
    }


    /**
     * [getListAll description]
     * Performance can be affected if the data is too large
     * @return  [type]  [return description]
     */
    public static function getListAll()
    {
        if (self::$getList === null) {
            self::$getList = self::where('store_id', session('adminStoreId'))
                ->get()->keyBy('id');
        }
        return self::$getList;
    }

}
