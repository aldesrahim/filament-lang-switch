<?php

declare(strict_types=1);

namespace Aldesrahim\FilamentLangSwitch\Concerns;

use Illuminate\Database\Eloquent\Model;

trait InteractsWithUser
{
    private string $authGuard = 'web';

    private bool $enableUserPreferredLocaleCache = true;

    public function setAuthGuard(string $guard): static
    {
        $this->authGuard = $guard;

        return $this;
    }

    public function getAuthGuard(): string
    {
        return $this->authGuard;
    }

    public function enableUserPreferredLocaleCache(bool $enable = true): static
    {
        $this->enableUserPreferredLocaleCache = $enable;

        return $this;
    }

    public function isUserPreferredLocaleCacheEnabled(): bool
    {
        return $this->enableUserPreferredLocaleCache;
    }

    public function getPreferredLocaleFromUser(): ?string
    {
        $authGuard = $this->getAuthGuard();
        /** @var ?Model */
        $user = auth()->guard($authGuard)->user();

        if (! $user) {
            return null;
        }

        if ($this->isUserPreferredLocaleCacheEnabled() && $preferredLocale = $this->getPreferredLocaleFromUserCache($user)) {
            return $preferredLocale;
        }

        /** @var class-string<Model> */
        $preferredLocaleModel = config('filament-lang-switch.models.preferred_locale');
        $preferredLocale = $preferredLocaleModel::query()
            ->where([
                'model_type' => $user->getMorphClass(),
                'model_id' => $user->getKey(),
            ])
            ->value('locale');

        if ($preferredLocale && $this->isLocaleAvailable($preferredLocale)) {
            return $preferredLocale;
        }

        return null;
    }

    public function setPreferredLocaleToUser(string $locale): void
    {
        $authGuard = $this->getAuthGuard();
        /** @var ?Model */
        $user = auth()->guard($authGuard)->user();

        if (! $user) {
            return;
        }

        if (! $this->isLocaleAvailable($locale)) {
            return;
        }

        /** @var class-string<Model> */
        $preferredLocaleModel = config('filament-lang-switch.models.preferred_locale');
        $preferredLocaleModel::query()->updateOrCreate(
            [
                'model_type' => $user->getMorphClass(),
                'model_id' => $user->getKey(),
            ],
            ['locale' => $locale],
        );

        if ($this->isUserPreferredLocaleCacheEnabled()) {
            $this->cachePreferredLocaleForUser($user, $locale);
        }
    }

    public function getPreferredLocaleFromUserCache(Model $model): ?string
    {
        $modelKey = md5($model->getMorphClass().$model->getKey());
        $cacheKey = config('filament-lang-switch.stores.user.cache.prefix').$modelKey;

        $preferredLocale = cache()->get($cacheKey);

        if ($preferredLocale && $this->isLocaleAvailable($preferredLocale)) {
            return $preferredLocale;
        }

        $this->clearPreferredLocaleUserCache($model);

        return null;
    }

    public function cachePreferredLocaleForUser(Model $model, string $locale): void
    {
        $modelKey = md5($model->getMorphClass().$model->getKey());
        $cacheKey = config('filament-lang-switch.stores.user.cache.prefix').$modelKey;
        $cacheTtl = config('filament-lang-switch.stores.user.cache.minutes');

        cache()->put(
            key: $cacheKey,
            value: $locale,
            ttl: now()->addMinutes($cacheTtl),
        );
    }

    public function clearPreferredLocaleUserCache(Model $model): void
    {
        $modelKey = md5($model->getMorphClass().$model->getKey());
        $cacheKey = config('filament-lang-switch.stores.user.cache.prefix').$modelKey;

        cache()->forget($cacheKey);
    }
}
