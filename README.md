# LIMONAIA · Cucina di Costiera

Prototype commercial one-page pour un restaurant fictif de cuisine de la côte amalfitaine à Parme
(« un pezzo di Costiera a Parma »). PHP 8 natif, sans framework ni base de données, une seule page
`index.php` et trois endpoints JSON de simulation.

Palette « Costiera » : Calce `#FBF6E9`, Limone `#F2CF3A`, Blu Maiolica `#1B4B9B`, Blu Notte `#0F2547`,
Azzurro Cielo `#D6E8F3`, Foglia `#3E6B3A`, Cotto `#C8643B`.
Titres *Instrument Serif* (romain + italique), interface *Figtree*.

---

## Lancer le site

```bash
php -S localhost:8000
```

Aucune installation, aucune dépendance PHP. Toutes les bibliothèques (GSAP 3.13 + ScrollTrigger +
SplitText + Flip + Draggable, Lenis, Swiper 11) et les deux polices sont **servies localement**
depuis `assets/vendor/` et `assets/fonts/` : le prototype fonctionne hors ligne et sans CDN.
PHP ≥ 8.0 suffit.

---

## Arborescence

```
index.php                 page unique : helpers e() / t() / bi() / img() / wave()
config.php                TOUTE la donnée du site (marque, couleurs, menu, i18n, horaires…)
partials/
  head.php                :root depuis config, SEO, Open Graph, JSON-LD Restaurant
  preloader.php           citron qui roule, vague bleue, compteur « …87% »
  icons-svg.php           49 <symbol> dessinés main : citrons, vagues, gozzo, 6 carreaux
  header.php              pilule crème flottante, pastille ouvert/fermé, IT/EN, panier-cassette
  hero.php                trois arches, titre derrière l'arche centrale, citrons suspendus
  costiera.php            histoire, compteurs animés, sceau « Fatto a mano »
  piatti.php              carrousel Swiper centré sur une ligne ondulée
  menu.php                onglets-carreaux, filtres, 28 plats en cartes horizontales
  giro.php                carte de la côte en SVG, gozzo, menu dégustation € 45 → € 38
  booking.php             stepper de citrons, 3 vues, calendrier de carreaux, cartolina
  gallery.php             3 colonnes en parallaxe + lightbox Flip
  reviews.php             marquee pausable de 5 avis, 4,8 / 5
  info.php                carte illustrée SVG, horaires en direct, contacts
  footer.php              mot de la marque rempli d'eau, newsletter, réseaux, Torna su
  cart-drawer.php         cassette à citrons, livraison/asporto, annulation 5 s
  checkout-modal.php      3 étapes, billet de bord perforé, suivi par gozzo
  toast.php               toasts empilables aria-live
api/
  _bootstrap.php          JSON, validation, codes simulés
  slots.php               créneaux Pranzo / Aperitivo al tramonto / Cena par date
  booking.php             réservation → LMN-T-XXXX + fichier .ics
  order.php               commande → LMN-XXXX, frais et minimum recalculés côté serveur
assets/css/style.css      CSS écrit main, variables, vagues, curseur-tranche de citron
assets/js/                main, hero, piatti, menu, giro, cart, checkout, booking, gallery, i18n
assets/img/               WebP + JPG (< 250 Ko), /piatti pour les 28 plats
assets/vendor/            GSAP, ScrollTrigger, SplitText, Flip, Draggable, Lenis, Swiper
assets/fonts/             Instrument Serif (romain, italique), Figtree 400/500/600
```

---

## Rebrander le site

Tout est dans `config.php`. Changer `brand.name`, `colors` et `menu` rebrande l'ensemble du site :
les couleurs sont injectées dans `:root` par `partials/head.php`, le menu est rendu en PHP **et**
exposé en JSON (`window.LIMONAIA`) pour le carrousel et le panier, les textes passent par `i18n`
(196 clés en italien et en anglais).

| Clé | Effet |
|---|---|
| `brand.name` / `subtitle` / `tagline` | logo, `<title>`, JSON-LD, footer, billet de bord, épingle de la carte |
| `colors` | `:root` (CSS), donc toutes les sections, vagues et SVG |
| `menu` | onglets, cartes, filtres, `window.LIMONAIA.menu`, prix recalculés par `/api/order.php` |
| `signature` | carrousel « I piatti del sole » |
| `giro` | carte de la côte, étapes, prix du menu dégustation |
| `booking` | convives, vues, occasions, groupes de créneaux |
| `delivery` | frais € 3, gratuit dès € 30, minimum € 15, délais |
| `hours` / `sunset` | pastille ouvert/fermé (Europe/Rome), coucher du soleil par mois |
| `gallery` / `reviews` | galerie et marquee |
| `i18n` | bascule IT / EN sans rechargement (italien par défaut, `lang="it"`) |
| `demo_badge` | pastille « Demo » en bas à gauche |

---

## API de simulation

Toutes répondent en JSON, valident les entrées et ne touchent aucune base de données.

```bash
# Créneaux d'une date (les lundis sont fermés)
curl "localhost:8000/api/slots.php?date=2026-11-13&guests=4"

# Réservation → LMN-T-XXXX + résumé + .ics
curl -X POST localhost:8000/api/booking.php -H 'Content-Type: application/json' \
  -d '{"date":"2026-11-20","time":"20:00","guests":4,"view":"terrazza",
       "name":"Chiara","phone":"+39 340 9988776","email":"chiara@example.it","consent":true}'

# Commande → LMN-XXXX, frais et minimum vérifiés côté serveur
curl -X POST localhost:8000/api/order.php -H 'Content-Type: application/json' \
  -d '{"mode":"delivery","items":[{"slug":"spaghetti-ai-limoni-di-costiera","qty":2}],
       "customer":{"name":"Luca","phone":"+39 333 1234567","email":"luca@example.it",
                   "address":"Via Verdi 4, Parma"}}'
```

Les prix ne viennent **jamais** du client : `/api/order.php` les relit dans `config.php`.
Le Giro della Costiera est accepté comme un seul article à `€ 38`.

---

## Détails faits main

- 6 carreaux de faïence composés en SVG (`i-tile-1` … `i-tile-6`) : onglets, cadre de la carte,
  calendrier de réservation (un motif différent chaque jour) et frise de séparation.
- Calendrier : lundi fermé, jours passés désactivés, flip 3D de 500 ms révélant `Tramonto HH:MM`
  calculé depuis la table `sunset` de `config.php`.
- Réservation : carte postale avec timbre, tampon `COSTIERA`, faux QR en SVG et vrai fichier `.ics`.
- Commande : billet de bord perforé, tampon `PAGATO` animé, suivi par gozzo sur quatre ports.
- Footer : le mot de la marque se remplit d'eau (vague SVG) au scroll, ondulations au survol.
- Aucun emoji : citrons, soleil, poissons, bateaux et vagues sont des SVG.

## Accessibilité et performance

- `lang="it"` par défaut, mis à jour par la bascule IT / EN ; sections `aria-labelledby`.
- Contraste AA vérifié sur toutes les combinaisons texte/fond (le Cotto « encre » `#A64823` sert au
  petit texte terre cuite sur fonds clairs).
- Focus visible, piège et restauration du focus dans le tiroir, les modales et la lightbox,
  `Échap` pour fermer, calendrier utilisable aux flèches.
- Décoratif en `aria-hidden="true"`, images informatives avec `alt`, champs avec label.
- `prefers-reduced-motion` : pas de parallaxe ni de balancement, vagues statiques, pas de préloader.
- Images WebP + JPG en `loading="lazy"` avec `width`/`height` ; animations limitées à `transform`,
  `opacity` et `clip-path`.

## Limites assumées (prototype)

- Réservation, paiement, newsletter et commande sont **simulés** : aucune donnée n'est conservée,
  les codes `LMN-XXXX` sortent d'un générateur aléatoire.
- La carte « Dove siamo » est une illustration SVG, volontairement sans iframe.
- Le `hero-loop.mp4` optionnel n'est pas inclus ; le hero repose sur les trois photos en arches.
