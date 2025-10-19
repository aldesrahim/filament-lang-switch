<?php

declare(strict_types=1);

namespace Aldesrahim\FilamentLangSwitch;

final class FilamentLangSwitch
{
    use Concerns\InteractsWithCookies;
    use Concerns\InteractsWithSession;
    use Concerns\InteractsWithUser;

    /**
     * @param  array<string, array{label: string}>  $availableLocales
     */
    public function __construct(
        private readonly array $availableLocales
    ) {}

    /**
     * @return array<string, array{label: string}>
     */
    public function getAvailableLocales(): array
    {
        return $this->availableLocales;
    }

    public function isLocaleAvailable(string $locale): bool
    {
        return array_key_exists($locale, $this->availableLocales);
    }

    public function getPreferredLocale(): ?string
    {
        return $this->getPreferredLocaleFromUser()
            ?? $this->getPreferredLocaleFromSession()
            ?? $this->getPreferredLocaleFromCookie();
    }

    public function setPreferredLocale(string $locale): void
    {
        $this->setPreferredLocaleToUser($locale);
        $this->setPreferredLocaleToSession($locale);
        $this->setPreferredLocaleToCookie($locale);

        event(new Events\PreferredLocaleSwitched($locale));
    }
}
