<?php

namespace App\Livewire;

use App\Events\BrowserEvent;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Livewire\Component;

class SettingDropdown extends Component
{
    public const LOCALES = ['en', 'vi', 'ja'];

    public const THEMES = ['theme-light', 'theme-dark', 'auto'];

    public $theme;

    public $locale;

    public function mount(): void
    {
        $this->theme = Session::get('theme', 'auto');

        $this->locale = App::currentLocale();
    }

    public function setLocale($locale): void
    {
        if (! in_array($locale, self::LOCALES, true)) {
            return;
        }

        $this->locale = $locale;

        Session::put('locale', $locale);
    }

    public function setTheme($theme): void
    {
        if (! in_array($theme, self::THEMES, true)) {
            return;
        }

        $this->theme = $theme;

        Session::put('theme', $theme);

        $this->dispatch(BrowserEvent::THEME_CHANGED, theme: $theme);
    }

    public function render(): View
    {
        return view('livewire.setting-dropdown');
    }
}
