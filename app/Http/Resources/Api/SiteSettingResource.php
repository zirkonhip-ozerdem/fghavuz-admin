<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\SiteSetting
 */
class SiteSettingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = current_api_locale();

        return [
            'site_name' => $this->site_name,
            'logo' => $this->logoUrl($this->logo),
            'dark_logo' => $this->logoUrl($this->dark_logo),
            'favicon' => $this->logoUrl($this->favicon),
            'footer_logo' => $this->logoUrl($this->footer_logo),
            'phone' => $this->phone,
            'email' => $this->email,
            'whatsapp' => $this->whatsapp,
            'address' => $this->address,
            'map_embed_url' => $this->map_embed_url,
            'social' => [
                'instagram' => $this->instagram_url,
                'linkedin' => $this->linkedin_url,
                'facebook' => $this->facebook_url,
            ],
            'default_locale' => $this->default_locale,
            'active_locales' => $this->active_locales,
            'maintenance_mode' => (bool) $this->maintenance_mode,
            'footer_text' => $this->getTranslation('footer_text', $locale, false),
            'copyright_text' => $this->copyright_text,
            'yengec_yazilim_url' => $this->yengec_yazilim_url,
            'yna_ekibi_url' => $this->yna_ekibi_url,
        ];
    }

    private function logoUrl(?string $path): ?string
    {
        return $path ? asset('storage/'.$path) : null;
    }
}
