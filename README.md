# Projekty CHC

Web s projekty studentů Creative Hill College (vývojáři, grafici, filmaři) s administrací pro správu příspěvků, kategorií a uživatelů.

Postaveno na Laravelu 11, Tailwindu, Alpine.js a TinyMCE.

## Role v administraci

| | Editor | Administrátor |
|---|---|---|
| Vytvářet příspěvky | ano | ano |
| Upravovat, publikovat a mazat příspěvky | jen svoje | všechny |
| Spravovat kategorie | ne | ano |
| Spravovat uživatele | ne | ano |

Příspěvek může být **koncept** (neveřejný), **publikovaný**, nebo **naplánovaný** (zveřejní se v zadaném čase). Koncepty si autor i administrátor mohou prohlédnout přes odkaz „Náhled“.

## Lokální spuštění

Požadavky: PHP 8.2 s rozšířeními `gd`, `pdo_sqlite` (nebo `pdo_mysql`), Composer, Node.js.

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed --class=CategorySeeder
php artisan storage:link
php artisan app:create-admin
php artisan serve
```

Lokálně se používá SQLite (`DB_CONNECTION=sqlite`, databáze v `database/database.sqlite`), produkce běží na MySQL.

## Frontend

```bash
npm run dev     # vývoj s automatickým obnovováním
npm run build   # produkční build do public/build (je součástí repozitáře)
```

Po změně šablon nebo JS je potřeba spustit `npm run build` a výsledek commitnout.

## Testy

```bash
php artisan test
```

Testy běží nad SQLite v paměti a lokální databáze se jich nedotkne.

## Nasazení na produkci

Běžné nasazení:

```bash
php artisan down
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear && php artisan optimize
php artisan up
```

### První nasazení po přepsání historie (září 2026)

Historie repozitáře byla 24. 9. 2026 přepsána (odstranění citlivých souborů), takže na serveru `git pull` nepůjde. Postup:

1. **Záloha** (bez ní nepokračovat):
   ```bash
   mysqldump -u UZIVATEL -p NAZEV_DB > zaloha-$(date +%F).sql
   tar czf uploady-$(date +%F).tar.gz storage/app/public
   git rev-parse HEAD > puvodni-commit.txt
   ```
2. **Kontrola serveru**: `git status` musí být čistý. Jestli ukáže upravené soubory, někdo měnil kód přímo na serveru; ty změny si nejdřív ulož.
3. **Náhled migrací bez spuštění**:
   ```bash
   php artisan migrate:status
   php artisan migrate --pretend   # vypíše SQL, nic nezmění
   ```
   Čekají jen tři migrace z 25. 9. 2026. Pokud čeká i `add_slug_to_posts_table`, nebyla dřív nasazena. Poběží spolu s nimi.
4. **Nasazení**:
   ```bash
   php artisan down
   git fetch origin
   git reset --hard origin/master
   composer install --no-dev --optimize-autoloader
   php artisan migrate --force
   php artisan optimize:clear && php artisan optimize
   php artisan up
   ```
   `reset --hard` smaže ze serveru soubory, které z repozitáře zmizely (`composer.phar`, `public/info.php`, `public/test.php`, `database.sql`, `creativniprojekty`, `c/`). `.env`, `vendor/` a nahrané obrázky se nemění. Pokud jsi na serveru spouštěl Composer přes `composer.phar`, stáhni ho znovu nebo použij globální `composer`.
5. **Kontrola**: homepage, detail příspěvku s galerií, přihlášení a administrace. Počet příspěvků by měl odpovídat stavu před nasazením. Podpora WebP: `php -r "var_dump((bool) (imagetypes() & IMG_WEBP));"`. Bez ní se obrázky ukládají v originále.
6. **Heslo**: pokud má účet `admin` pořád heslo `admin`, hned ho změň v Můj profil.

**Návrat zpět**, kdyby něco nefungovalo:

```bash
php artisan down
php artisan migrate:rollback --step=3 --force
git reset --hard $(cat puvodni-commit.txt)
composer install --no-dev --optimize-autoloader
php artisan optimize:clear
php artisan up
```

Kdyby nefungoval ani rollback, obnov databázi ze zálohy (`mysql -u UZIVATEL -p NAZEV_DB < zaloha-….sql`).

Co nasazení dělá s existujícími daty:

- Všichni stávající uživatelé dostanou roli administrátor. Stávající příspěvky dostanou datum publikace podle data vytvoření, zůstanou tedy veřejné. Žádná data se nemažou.
- Stávající obrázky zůstávají, jak jsou. Zmenšují se a do WebP převádějí jen nově nahrané (galerie max. 1920 px, náhled max. 1200 px, GIFy beze změny).
- Obsah příspěvků se čistí (HTMLPurifier) až při dalším uložení. Vložená videa jsou povolena jen z YouTube a Vimea, jiné vložené prvky (např. mapy) se při uložení odstraní.
