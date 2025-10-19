<?php

declare(strict_types=1);

namespace Aldesrahim\FilamentLangSwitch\Concerns;

trait InteractsWithSession
{
    private string $sessionKey = 'filament_lang_switch_locale';

    public function setSessionKey(string $key): static
    {
        $this->sessionKey = $key;

        return $this;
    }

    public function getSessionKey(): string
    {
        return $this->sessionKey;
    }

    public function getPreferredLocaleFromSession(): ?string
    {
        $key = $this->getSessionKey();
        $locale = session()->get($key);

        if ($locale && $this->isLocaleAvailable($locale)) {
            return $locale;
        }

        return null;
    }

    public function setPreferredLocaleToSession(string $locale): void
    {
        $key = $this->getSessionKey();

        if (! $this->isLocaleAvailable($locale)) {
            return;
        }

        session()->put($key, $locale);
    }
}
