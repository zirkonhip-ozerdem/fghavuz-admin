<?php

namespace App\Support;

/**
 * Rol ve izin isimlerini tek merkezden yonetmek icin.
 * RolePermissionSeeder ve Filament Resource::canXxx() metodlarinda kullanilir.
 */
final class Permissions
{
    public const MANAGE_SITE_SETTINGS = 'manage_site_settings';

    public const MANAGE_CORPORATE = 'manage_corporate';

    public const MANAGE_PRODUCTS = 'manage_products';

    public const MANAGE_BLOG = 'manage_blog';

    public const MANAGE_CATALOGS = 'manage_catalogs';

    public const VIEW_CONTACT_MESSAGES = 'view_contact_messages';

    public const MANAGE_CONTACT_MESSAGES = 'manage_contact_messages';

    public const VIEW_QUOTE_REQUESTS = 'view_quote_requests';

    public const MANAGE_QUOTE_REQUESTS = 'manage_quote_requests';

    public const MANAGE_SEO = 'manage_seo';

    public const MANAGE_REFERENCES = 'manage_references';

    public const MANAGE_MEDIA = 'manage_media';

    public const MANAGE_USERS = 'manage_users';

    public static function all(): array
    {
        return [
            self::MANAGE_SITE_SETTINGS,
            self::MANAGE_CORPORATE,
            self::MANAGE_PRODUCTS,
            self::MANAGE_BLOG,
            self::MANAGE_CATALOGS,
            self::VIEW_CONTACT_MESSAGES,
            self::MANAGE_CONTACT_MESSAGES,
            self::VIEW_QUOTE_REQUESTS,
            self::MANAGE_QUOTE_REQUESTS,
            self::MANAGE_SEO,
            self::MANAGE_REFERENCES,
            self::MANAGE_MEDIA,
            self::MANAGE_USERS,
        ];
    }

    public const ROLE_SUPER_ADMIN = 'super_admin';

    public const ROLE_ADMIN = 'admin';

    public const ROLE_EDITOR = 'editor';

    public const ROLE_SALES = 'sales';

    /**
     * Rol => bu role varsayilan atanacak izinler.
     * super_admin ayrica tum izinleri Gate::before ile otomatik gecer (bkz. User::can()).
     */
    public static function roleMatrix(): array
    {
        return [
            self::ROLE_SUPER_ADMIN => self::all(),
            self::ROLE_ADMIN => self::all(),
            self::ROLE_EDITOR => [
                self::MANAGE_CORPORATE,
                self::MANAGE_PRODUCTS,
                self::MANAGE_BLOG,
                self::MANAGE_CATALOGS,
                self::MANAGE_SEO,
                self::MANAGE_REFERENCES,
                self::MANAGE_MEDIA,
            ],
            self::ROLE_SALES => [
                self::VIEW_CONTACT_MESSAGES,
                self::MANAGE_CONTACT_MESSAGES,
                self::VIEW_QUOTE_REQUESTS,
                self::MANAGE_QUOTE_REQUESTS,
            ],
        ];
    }
}
