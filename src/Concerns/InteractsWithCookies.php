<?php

declare(strict_types=1);

namespace Aldesrahim\FilamentLangSwitch\Concerns;

trait InteractsWithCookies
{
    private string $cookieName = 'filament_lang_switch_locale';

    private int $cookieMinutes = 60 * 24;

    public function setCookieName(string $cookieName): static
    {
        $this->cookieName = $cookieName;

        return $this;
    }

    public function getCookieName(): string
    {
        return $this->cookieName;
    }

    public function setCookieMinutes(int $minutes): static
    {
        $this->cookieMinutes = $minutes;

        return $this;
    }

    public function getCookieMinutes(): int
    {
        return $this->cookieMinutes;
    }

    public function getPreferredLocaleFromCookie(): ?string
    {
        $cookieName = $this->getCookieName();
        $locale = request()->cookie($cookieName);

        if (is_string($locale) && $this->isLocaleAvailable($locale)) {
            return $locale;
        }

        return null;
    }

    public function setPreferredLocaleToCookie(string $locale): void
    {
        $cookieName = $this->getCookieName();
        $minutes = $this->getCookieMinutes();

        if (! $this->isLocaleAvailable($locale)) {
            return;
        }

        cookie()->queue(cookie()->make(
            name: $cookieName,
            value: $locale,
            minutes: $minutes,
        ));
    }
}
