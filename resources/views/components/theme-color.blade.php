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
    <style id="site-theme-override">:root { {{ $css }} }</style>
    <meta name="theme-color-custom" content="{{ $css }}">
    <script>
        document.addEventListener('livewire:navigated', function() {
            var meta = document.querySelector('meta[name="theme-color-custom"]');
            if (meta && meta.content) {
                var style = document.getElementById('site-theme-override');
                if (!style) {
                    style = document.createElement('style');
                    style.id = 'site-theme-override';
                    document.head.appendChild(style);
                }
                style.textContent = ':root { ' + meta.content + ' }';
            }
        });
    </script>
@endunless

