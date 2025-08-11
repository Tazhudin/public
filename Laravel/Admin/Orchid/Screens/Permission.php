<?php

namespace Admin\Orchid\Screens;

class Permission
{
    public const string CATALOG_READ = 'catalog';
    public const string CATALOG_EDIT = 'catalog.edit';
    public const string MARKETING_READ = 'marketing';
    public const string MARKETING_EDIT = 'marketing.edit';
    public const string FEEDBACK_READ  = 'feedback';
    public const string FEEDBACK_EDIT  = 'feedback.edit';
    public const string CUSTOMER  = 'customer';
    public const string SHOW_ORDER   = 'show_order';
    public const string CANCEL_ORDER = 'cancel_order';
    public const string DELIVERY = 'delivery';
    public const string DELIVERY_EDIT = 'delivery.edit';
    public const string DELIVERY_REMOVE = 'delivery.remove';
    public const string DELIVERY_SKLAD = 'delivery.sklad';
    public const string FEATURE_FLAGS = 'feature_flags';
    public const string SYSTEM = 'platform.system';
}
