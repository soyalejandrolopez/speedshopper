@unless (theme_color_is_default())
    @php
        $ramp = theme_color_ramp(theme_color());
        $css = '';

        foreach (['emerald', 'teal'] as $family) {
            foreach ($ramp as $shade => $hex) {
                $css .= '--color-'.$family.'-'.$shade.': '.$hex.'; ';
            }
        }
    @endphp
    <style>:root { {{ $css }} }</style>
    <meta name="theme-color-custom" content="{{ $css }}">
@endunless

