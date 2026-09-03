@unless (theme_color_is_default())
    @php
        $ramp = theme_color_ramp(theme_color());
        $css = '--theme-color: '.$ramp['500'].'; --theme-primary: '.$ramp['600'].'; --theme-primary-hover: '.$ramp['700'].'; --theme-primary-light: '.$ramp['50'].'; ';

        foreach (['emerald', 'teal', 'brand'] as $family) {
            foreach ($ramp as $shade => $hex) {
                $css .= '--color-'.$family.'-'.$shade.': '.$hex.'; ';
            }
        }
    @endphp
    <style>:root { {{ $css }} }</style>
    <meta name="theme-color-custom" content="{{ $css }}">
@endunless

