<?php

declare(strict_types=1);

use Aldesrahim\FilamentLangSwitch\Facades\FilamentLangSwitch as FacadeFilamentLangSwitch;
use Aldesrahim\FilamentLangSwitch\FilamentLangSwitch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\Factories\UserFactory;

it('retrieve configuration correctly', function () {
    $config = config('filament-lang-switch');

    expect($config)->toBeArray()
        ->and($config)->toHaveKey('available_locales')
        ->and($config)->toHaveKey('stores')
        ->and($config)->toHaveKey('tables')
        ->and($config)->toHaveKey('models')
        ->and($config)->toHaveKey('routes')
        ->and($config)->toHaveKey('middleware');
});

it('binds service correctly', function () {
    $byName = app('filament-lang-switch');
    $byFacade = FacadeFilamentLangSwitch::getFacadeRoot();
    $byClassName = app(FilamentLangSwitch::class);

    $expectation = FilamentLangSwitch::class;

    expect(get_class($byName))->toBe($expectation)
        ->and(get_class($byFacade))->toBe($expectation)
        ->and(get_class($byClassName))->toBe($expectation);
});

it('returns correct available locales', function () {
    setAvailableLocales();

    $preferredLocaleService = getPreferredLocaleService();

    $availableLocales = $preferredLocaleService->getAvailableLocales();

    expect($availableLocales)->toBeArray()
        ->and($availableLocales)->toHaveKey('en')
        ->and($availableLocales)->toHaveKey('id');

    expect($preferredLocaleService->isLocaleAvailable('en'))->toBeTrue()
        ->and($preferredLocaleService->isLocaleAvailable('id'))->toBeTrue()
        ->and($preferredLocaleService->isLocaleAvailable('fr'))->toBeFalse();
});

it('can retrieve preferred locale globally', function () {
    setAvailableLocales();

    $preferredLocaleService = getPreferredLocaleService();

    expect($preferredLocaleService->getPreferredLocale())->toBeNull();

    $preferredLocaleService->setPreferredLocale('fr');
    expect($preferredLocaleService->getPreferredLocale())->toBeNull();

    $preferredLocaleService->setPreferredLocale('id');
    expect($preferredLocaleService->getPreferredLocale())->toBe('id');

    // ignore changes when locale unavailable
    $preferredLocaleService->setPreferredLocale('fr');
    expect($preferredLocaleService->getPreferredLocale())->toBe('id');
});

it('interacts with session correctly', function () {
    setAvailableLocales();

    $sessionKey = config('filament-lang-switch.stores.session.session_key');

    $preferredLocaleService = getPreferredLocaleService();

    $preferredLocaleService->setPreferredLocaleToSession('fr');
    expect($preferredLocaleService->getPreferredLocaleFromSession())->toBeNull();

    $preferredLocaleService->setPreferredLocaleToSession('id');
    expect($preferredLocaleService->getPreferredLocaleFromSession())->toBe('id');

    // ignore changes when locale unavailable
    $preferredLocaleService->setPreferredLocaleToSession('fr');
    expect($preferredLocaleService->getPreferredLocaleFromSession())->toBe('id');

    session()->put($sessionKey, 'fr');
    expect($preferredLocaleService->getPreferredLocaleFromSession())->toBeNull();
});

it('interacts with cookies correctly', function () {
    setAvailableLocales();

    $cookieName = config('filament-lang-switch.stores.cookie.cookie_name');

    $preferredLocaleService = getPreferredLocaleService();

    $preferredLocaleService->setPreferredLocaleToCookie('fr');
    expect(cookie()->queued($cookieName)?->getValue())->toBeNull();

    $preferredLocaleService->setPreferredLocaleToCookie('id');
    expect(cookie()->queued($cookieName)?->getValue())->toBe('id');

    // ignore changes when locale unavailable
    $preferredLocaleService->setPreferredLocaleToCookie('fr');
    expect(cookie()->queued($cookieName)?->getValue())->toBe('id');

    Route::get($uri = '/get-preferred-locale', fn () => response()->json([
        'locale' => $preferredLocaleService->getPreferredLocaleFromCookie(),
    ]))->middleware(config('filament-lang-switch.routes.preferred_locale.middleware'));

    $this->withCookie($cookieName, 'fr')
        ->get($uri)->assertOk()->assertJson(['locale' => null]);

    $this->withCookie($cookieName, 'id')
        ->get($uri)->assertOk()->assertJson(['locale' => 'id']);
});

it('interacts with user correctly', function () {
    setAvailableLocales();

    /** @var Illuminate\Contracts\Auth\Authenticatable|Model */
    $user = UserFactory::new()->create();

    $modelKey = md5($user->getMorphClass().$user->getKey());
    $cacheKey = config('filament-lang-switch.stores.user.cache.prefix').$modelKey;

    $preferredLocaleService = getPreferredLocaleService()
        ->enableUserPreferredLocaleCache(true);

    expect($preferredLocaleService->getPreferredLocaleFromUser())->toBeNull();
    expect($preferredLocaleService->getPreferredLocaleFromUserCache($user))->toBeNull();

    Auth::login($user);

    $preferredLocaleService->setPreferredLocaleToUser('fr');
    expect($preferredLocaleService->getPreferredLocaleFromUser())->toBeNull();
    expect(cache()->get($cacheKey))->toBeNull();

    $preferredLocaleService->setPreferredLocaleToUser('id');
    expect($preferredLocaleService->getPreferredLocaleFromUser())->toBe('id');
    expect(cache()->get($cacheKey))->toBe('id');

    // ignore changes when locale unavailable
    $preferredLocaleService->setPreferredLocaleToUser('fr');
    expect($preferredLocaleService->getPreferredLocaleFromUser())->toBe('id');

    $preferredLocaleService->setPreferredLocaleToUser('id');
    $preferredLocaleService->clearPreferredLocaleUserCache($user);
    expect(cache()->get($cacheKey))->toBeNull();

    // cache already cleared at this point
    // test disabling cache
    $preferredLocaleService->enableUserPreferredLocaleCache(false);
    $preferredLocaleService->setPreferredLocaleToUser('en');
    expect($preferredLocaleService->getPreferredLocaleFromUser())->toBe('en');
    expect(cache()->get($cacheKey))->toBeNull();
});

it('interacts with user cache correctly', function () {
    setAvailableLocales();

    /** @var Illuminate\Contracts\Auth\Authenticatable|Model */
    $user = UserFactory::new()->create();

    $preferredLocaleService = getPreferredLocaleService();

    $preferredLocaleService->cachePreferredLocaleForUser($user, 'id');
    expect($preferredLocaleService->getPreferredLocaleFromUserCache($user))->toBe('id');

    $preferredLocaleService->cachePreferredLocaleForUser($user, 'fr');
    expect($preferredLocaleService->getPreferredLocaleFromUserCache($user))->toBeNull();
});

it('can change locale via controller', function () {
    setAvailableLocales();

    Route::get($uri = '/check-app-locale', fn () => response()->json([
        'locale' => app()->getLocale(),
    ]))->middleware([
        ...config('filament-lang-switch.routes.preferred_locale.middleware'),
        config('filament-lang-switch.middleware.preferred_locale'),
    ]);

    $this->get($uri)->assertOk()->assertJson(['locale' => config('app.locale')]);

    /** @var Illuminate\Contracts\Auth\Authenticatable|Model */
    $user = UserFactory::new()->create();

    Auth::login($user);

    $preferredLocaleService = getPreferredLocaleService();

    expect($preferredLocaleService->getPreferredLocale())->toBeNull();

    $sessionKey = config('filament-lang-switch.stores.session.session_key');
    $cookieName = config('filament-lang-switch.stores.cookie.cookie_name');
    $routeName = config('filament-lang-switch.routes.preferred_locale.name');

    $this->get(route($routeName, ['locale' => 'id']))
        ->assertRedirect()
        ->assertSessionHas($sessionKey, 'id')
        ->assertCookie($cookieName, 'id');

    expect($preferredLocaleService->getPreferredLocale())->toBe('id');
    $this->get($uri)->assertOk()->assertJson(['locale' => 'id']);

    $this->get(route($routeName, ['locale' => 'fr']))
        ->assertRedirect()
        ->assertSessionHas($sessionKey, 'id')
        ->assertCookie($cookieName, 'id');

    expect($preferredLocaleService->getPreferredLocale())->toBe('id');
    $this->get($uri)->assertOk()->assertJson(['locale' => 'id']);

    $this->get(route($routeName, ['locale' => 'en']))
        ->assertRedirect();

    expect($preferredLocaleService->getPreferredLocale())->toBe('en');
    $this->get($uri)->assertOk()->assertJson(['locale' => 'en']);
});
