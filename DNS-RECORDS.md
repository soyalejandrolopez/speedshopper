# Registros DNS y optimización — SpeedShopper

Dominio: **speedingshopper.com**
Panel: **cPanel** → **Zone Editor** (o el registrador donde apunte el DNS)

---

## 1) Registro CAA (control de emisión de certificados)

| Tipo | Nombre | Valor |
|---|---|---|
| CAA | `@` | `0 issue "letsencrypt.org"` |
| CAA | `@` | `0 issuewild "letsencrypt.org"` |
| CAA | `@` | `0 iodef "mailto:info@speedingshopper.com"` |

> En cPanel Zone Editor: registros **CAA** → host `@`, tipo `0`, tag `issue`/`issuewild`/`iodef`, valor como arriba.

---

## 2) MTA-STS (cifrado forzado para correos entrantes)

**Registro TXT** en el subdominio `_mta-sts`:

| Tipo | Nombre | Valor |
|---|---|---|
| TXT | `_mta-sts` | `v=STSv1; id=1; mx: mail.speedingshopper.com;` |

**Archivo de política** (publícalo en tu hosting, no en DNS):
```
https://mta-sts.speedingshopper.com/.well-known/mta-sts.txt
```
Contenido:
```
version: STSv1
mode: enforce
mx: mail.speedingshopper.com
max_age: 604800
```

---

## 3) TLS-RPT (reportes de seguridad de correo)

| Tipo | Nombre | Valor |
|---|---|---|
| TXT | `_smtp._tls` | `v=TLSRPTv1; rua=mailto:info@speedingshopper.com` |

---

## 4) BIMI (indicador de marca en correos)

| Tipo | Nombre | Valor |
|---|---|---|
| TXT | `default._bimi` | `v=BIMI1; l=https://speedingshopper.com/logo.svg;` |

> `logo.svg` debe estar subido y accesible en tu web (p. ej. en `public/images/logo.svg`). Para que Gmail/Apple lo muestren se recomienda verificar con un VMC (opcional).

---

## 5) DNSSEC

1. cPanel → **DNSSEC** → **Enable DNSSEC**
2. Si el dominio usa los nameservers de Bluehost, se activa solo.
3. Si el dominio no está en Bluehost, activa DNSSEC en tu registrador y pega las DS records que te dé cPanel.

---

## 6) Seguridad del servidor (ocultar tecnología + velocidad)

cPanel → **MultiPHP INI Editor** → selecciona **speedingshopper.com** y guarda:

```ini
expose_php = Off
opcache.enable = On
opcache.memory_consumption = 128
opcache.max_accelerated_files = 10000
opcache.validate_timestamps = On
opcache.revalidate_freq = 60
```

> `expose_php=Off` quita el header `X-Powered-By` (el código ya lo intenta, pero PHP lo re-agrega si está On). OPcache acelera las respuestas (reduce TTFB → mejora LCP/FCP).

---

## 7) Aplicar cambios del código

```bash
cd ~/public_html/website_54d8238e/core
git pull origin main
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
