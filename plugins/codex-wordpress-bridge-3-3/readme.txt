=== Codex WordPress Bridge ===
Contributors: wpdevstudio
Tags: rest-api, acf, codex, content-management
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later

Безопасный REST-мост для управления страницами, записями, услугами, ACF и Rank Math SEO из Codex.

== Возможности ==

* Чтение списка страниц и записей.
* Чтение конкретной записи.
* Создание страницы или записи.
* Изменение заголовка, slug, контента, статуса, родителя и шаблона страницы.
* Чтение и обновление ACF через официальные функции ACF.
* Предпросмотр и замена ссылок в post_content.
* Журнал изменений в отдельной таблице.
* Специальная роль Codex Content Manager.
* Публикация новых материалов по умолчанию запрещена: publish превращается в draft.
* Загрузка JPG, PNG, GIF и WebP в медиатеку.
* Безопасная загрузка изображения по публичному URL.
* Назначение и удаление миниатюры записи.
* Поиск дублей по URL и SHA-256 содержимого.
* Лимит изображения 10 МБ с возможностью изменения через фильтр cwb_max_media_size.
* CPT `service` включён в стандартный whitelist.
* Роль Codex Content Manager автоматически получает capabilities всех разрешённых CPT и их таксономий.
* GET/PATCH `/posts/{id}/seo` для безопасного чтения и изменения Rank Math SEO.
* SEO allowlist: title, description, focus keyword, canonical URL и robots.
* SEO PATCH запрещает изменение ID, slug, URL, post_type, status, контента и остальных полей записи.
* После SEO PATCH Bridge повторно читает объект из WordPress и возвращает полный объект, ACF и SEO/meta.
* Полный объект записи содержит безопасно экспонированные ACF и frontend meta. Потенциально секретные ACF-поля по умолчанию скрыты.
* Все зарегистрированные типы записей HivePress (`hp_*`) автоматически доступны без ручного whitelist.
* Полные ответы HivePress-записей содержат `hivepress_meta` и назначенные термины `taxonomies`.
* Создание и обновление HivePress-записей поддерживает безопасные поля `hivepress_meta` и `taxonomies`.


== Установка ==

1. Установите ZIP через Плагины → Добавить плагин → Загрузить плагин.
2. Активируйте плагин.
3. Создайте отдельного пользователя WordPress.
4. Назначьте ему роль Codex Content Manager.
5. В профиле пользователя создайте пароль приложения.
6. Используйте Basic Auth: логин пользователя + пароль приложения.

== Проверка ==

GET /wp-json/codex-bridge/v1/health

== Основные маршруты ==

GET  /wp-json/codex-bridge/v1/posts?post_type=page&search=Доставка
GET  /wp-json/codex-bridge/v1/posts?post_type=hp_listing&status=any&per_page=100&page=1
GET  /wp-json/codex-bridge/v1/post-types
POST /wp-json/codex-bridge/v1/posts
GET  /wp-json/codex-bridge/v1/posts/123
PATCH /wp-json/codex-bridge/v1/posts/123
GET  /wp-json/codex-bridge/v1/posts/123/acf
PATCH /wp-json/codex-bridge/v1/posts/123/acf
GET  /wp-json/codex-bridge/v1/posts/123/seo
PATCH /wp-json/codex-bridge/v1/posts/123/seo
POST /wp-json/codex-bridge/v1/links/scan
POST /wp-json/codex-bridge/v1/links/replace
GET  /wp-json/codex-bridge/v1/audit
POST /wp-json/codex-bridge/v1/media/upload
POST /wp-json/codex-bridge/v1/media/sideload
PATCH /wp-json/codex-bridge/v1/posts/123/thumbnail

== Примеры JSON ==

Создание черновика:

{
  "post_type": "page",
  "title": "Доставка и оплата",
  "slug": "delivery",
  "content": "<h2>Доставка</h2>",
  "status": "draft"
}

Обновление страницы:

{
  "title": "Новое название",
  "content": "<p>Новый текст</p>"
}

Обновление объявления HivePress (значения таксономий — ID существующих терминов):

{
  "title": "Новое название объявления",
  "hivepress_meta": {
    "hp_price": 1500,
    "hp_featured": true
  },
  "taxonomies": {
    "hp_listing_category": [12, 18]
  }
}

Обновление ACF:

{
  "fields": {
    "hero_title": "Новый заголовок",
    "field_65a381d911222": "Можно передавать и ключ поля"
  }
}

Предпросмотр замены ссылок:

{
  "old": "/old-url/",
  "new": "/new-url/",
  "apply": false
}

Применение:

{
  "old": "/old-url/",
  "new": "/new-url/",
  "apply": true
}

== Расширение ==

Разрешить собственный тип записи:

add_filter( 'cwb_allowed_post_types', function ( $types ) {
    $types[] = 'wpds-case';
    return $types;
} );

Разрешить публикацию:

add_filter( 'cwb_allow_publish', '__return_true' );

Рекомендуется добавлять фильтры в MU-плагин или дочернюю тему, а не менять этот плагин напрямую.

== Ограничения MVP ==

* Замена ссылок выполняется только в post_content. ACF проверяется и меняется отдельными запросами.
* Удаление записей отсутствует намеренно.
* Работа с пользователями, настройками WordPress, заказами и прямым SQL отсутствует.
* Сложные ACF-поля поддерживаются функцией update_field, но входная JSON-структура должна соответствовать структуре конкретного поля.


== Работа с изображениями ==

Загрузка локального файла выполняется multipart/form-data. Поле файла: file.
Дополнительные поля: post_id, set_featured, title, alt, caption, description.

Пример загрузки по URL:

{
  "url": "https://example.com/image.webp",
  "post_id": 123,
  "set_featured": true,
  "title": "Заголовок изображения",
  "alt": "Описание изображения",
  "caption": "Подпись",
  "description": "Источник: example.com"
}

Назначить уже загруженное изображение:

{
  "attachment_id": 456
}

Удалить миниатюру:

{
  "attachment_id": 0
}

Также поле featured_media можно передавать при создании или обновлении записи.

Изменить лимит размера:

add_filter( 'cwb_max_media_size', function () {
    return 20 * MB_IN_BYTES;
} );


== 0.4.0 ==
* Добавлен `wpds-case` в стандартный whitelist типов записей.
* Media upload сохраняет `source_url` внешней страницы/скриншота.
* Ответ media дополнен caption и filesize.
* Улучшена связка с Codex Cloud capture: screenshot -> WebP -> Media -> Gutenberg.

== Screenshot Worker (0.5.0) ==
Endpoint: POST /wp-json/codex-bridge/v1/screenshot/capture
The browser runs on the WordPress server, so Codex Cloud only needs network access to the WordPress domain.
On first capture the plugin automatically creates wp-content/uploads/codex-bridge-runtime, installs Playwright/Sharp there and downloads Chromium.
Server requirements: node, npm, PHP exec(), outbound HTTPS. Private/reserved/local URLs are rejected.


== Rank Math SEO (0.6.0) ==

Чтение:
GET /wp-json/codex-bridge/v1/posts/123/seo

Безопасное обновление:

{
  "rank_math_title": "Разработка плагинов WordPress — WP DevStudio",
  "rank_math_description": "Разработка кастомных WordPress-плагинов под бизнес-задачи.",
  "rank_math_focus_keyword": "разработка плагинов WordPress",
  "rank_math_canonical_url": "https://example.com/services/plugin-development/",
  "rank_math_robots": ["index", "follow"]
}

Допустимы также короткие alias: seo_title, seo_description, focus_keyword, canonical_url, robots.
Поля id, slug, url, post_type, status, title, content и другие поля записи через SEO endpoint намеренно запрещены.

`canonical_fallback`, `robots_fallback` и `h1_candidates` помогают аудиту, но итоговые canonical/robots/H1 необходимо проверять в публичном HTML: Rank Math и тема могут применять глобальные настройки и фильтры.

Для добавления дополнительных безопасных post meta используйте фильтр `cwb_frontend_meta_keys`.
Для управления доступностью отдельных ACF-полей используйте фильтр `cwb_expose_acf_field`.

== 0.6.0 ==
* `service` добавлен в whitelist ядра Bridge.
* Capabilities роли обновляются для всех разрешённых CPT.
* Добавлен Rank Math SEO endpoint с GET/PATCH.
* Добавлены canonical URL и robots.
* После PATCH возвращается повторно прочитанный полный объект.
* В полный объект добавлены безопасные ACF/frontend meta для аудита.
* Добавлена защита от изменения идентичности/URL/статуса записи через SEO PATCH.

== 0.7.0 ==
* Bridge автоматически обнаруживает все зарегистрированные типы записей HivePress и расширений по префиксу `hp_`.
* Добавлен GET `/post-types` для просмотра доступных типов, таксономий и поддерживаемых возможностей.
* Чтение, создание и обновление HivePress-записей дополнено meta-полями `hp_*` и таксономиями `hp_*`.
* Секретоподобные meta-ключи не выдаются и не принимаются.

== HivePress API (1.0.0) ==

Bridge 1.0 exposes normalized resources; clients do not send arbitrary post meta.
All routes require `use_codex_bridge`. Every write additionally checks the native
post-type or taxonomy capability. Publishing `hp_listing`,
`hp_listing_attribute`, or `hp_vendor` additionally requires
`publish_codex_bridge_content`. There are no delete routes.

=== Routes ===

* `GET /taxonomies` — allowed taxonomy discovery.
* `GET|POST /taxonomies/{taxonomy}/terms` — list/create terms.
* `GET|PATCH /taxonomies/{taxonomy}/terms/{id}` — read/update a term.
* `GET|POST /hivepress/listing-attributes` — list/create listing attributes.
* `GET|PATCH /hivepress/listing-attributes/{id}` — read/update an attribute.
* `GET|POST /hivepress/listing-attributes/{id}/options` — list/create select options.
* `PATCH /hivepress/listing-attributes/{id}/options/{option_id}` — update an option.
* `GET /hivepress/listings/schema` — runtime listing/attribute contract.
* `GET|POST /hivepress/listings` — list/create listings.
* `GET|PATCH /hivepress/listings/{id}` — read/update a listing.
* `GET|POST /hivepress/vendors` — list/create vendors.
* `GET|PATCH /hivepress/vendors/{id}` — read/update a vendor.
* `GET /users` — safe user identity and relevant capabilities (never email).
* `GET /media` and `GET /media/{id}` — attachment inspection without server paths.

=== Taxonomy schemas ===

`GET /taxonomies` returns `{items:[{name,label,object_types,hierarchical,
capabilities,hivepress,writable,supported_operations}]}`. A term is
`{id,taxonomy,name,slug,description,parent,count,url}`. List filters are `search`,
`slug`, `parent`, `hide_empty`, `per_page` (maximum 100), and `page`; the envelope
is `{items,total,total_pages,page}`. Create accepts `name` (required), `slug`,
`description`, and `parent`; PATCH accepts those same four fields. Slug collisions,
foreign parents, hierarchy cycles, and taxonomies outside allowed post types are
rejected.

=== Listing attribute schema ===

An attribute response is `{id,label,slug,status,field_type,editable,required,
filterable,searchable,search_field_type,sortable,display_format,display_areas,
decimals,min_value,max_value,category_ids,option_taxonomy,options}`. Create requires
`label` and `slug`, supports `field_type` `text`, `number`, or `select`, and accepts
all remaining response settings plus `options`. PATCH is partial. `display_areas`
is restricted to HivePress block/page primary, secondary, and ternary areas.
Number precision is 0–6. Category IDs must be `hp_listing_category` terms.

A select option request is `{name,slug?,description?,parent?}` and its response is
the term schema. The Bridge derives HivePress's `hp_listing_{attribute}` taxonomy,
registers it for the current request after an attribute write, clears the relevant
HivePress model cache, and returns real term IDs. Options are always assigned to a
listing by ID, never by their display label.

Example attribute request:

    {
      "label":"Condition","slug":"condition","field_type":"select",
      "editable":true,"required":false,"filterable":true,
      "search_field_type":"select","sortable":false,
      "display_format":"%label%: %value%",
      "display_areas":["view_block_secondary","view_page_secondary"],
      "category_ids":[123],
      "options":[{"name":"New","slug":"new"},{"name":"Good","slug":"good"}]
    }

=== Listing schema and CRUD ===

`GET /hivepress/listings/schema` describes the detected category taxonomy,
vendor type, normalized fields, every published attribute, its internal field,
expected value, select taxonomy/options, price discovery, geolocation availability,
media support, statuses, and the caller's publication permission.

Create/PATCH accepts only `{title,slug,content|description,status,category_id,
author_id,vendor_id,price,location,coordinates,featured_media,gallery_media,
attributes}`. `attributes` is keyed by attribute slug. Text values are strings,
number values are numeric and rounded to the configured decimals, and select values
must be option term IDs belonging to that attribute taxonomy. Price is represented
by the published `price` attribute when installed; send either top-level `price` or
`attributes.price` (the schema reports whether it exists). Location is accepted
only when HivePress Geolocation is active. `coordinates` may contain `latitude` and
`longitude`.

A listing response is `{id,title,slug,status,url,permalink,content,category,author,
vendor,price,formatted_price,location,coordinates,featured_media,gallery_media,
attributes,created,modified}`. Each attribute contains `{attribute_id,label,slug,
type,raw_value,display_value,option_id}`. Writes return a freshly read object.

Example:

    {
      "title":"Example","content":"Description","status":"draft",
      "category_id":123,"vendor_id":456,"featured_media":800,
      "gallery_media":[800,801],
      "attributes":{"manufacturer":701,"model":"3CX","year":2021}
    }

`author_id` defaults to the vendor's `post_author` when a vendor is supplied, and
the Bridge rejects mismatched author/vendor pairs. Gallery items must be existing
image attachments and are attached using HivePress's `images` parent field.

=== Vendors, users, and media ===

Vendor create accepts `{name,slug?,description?,status?,user_id?,
create_demo_user?,featured_media?,attributes?}`. An existing `user_id` is preferred.
`create_demo_user:true` additionally requires WordPress `create_users`; it creates
a subscriber with a random password and an `example.invalid` email. Passwords are
neither accepted nor returned. Vendor responses expose `user_id` and safe user
identity only. Vendor PATCH does not create or modify users.

`GET /users` returns `{items:[{id,display_name,slug,roles,capabilities}]}` and never
returns email. Media list filters are `search`, `mime_type`, `parent`, `per_page`,
and `page`. Media objects are `{id,title,alt,caption,description,mime_type,width,
height,filesize,url,source_url,parent,created,modified}`; filesystem paths are never
returned. Existing upload and sideload routes are unchanged.

=== HivePress storage verified for this implementation ===

HivePress 1.7.31 registers `hp_listing`, `hp_vendor`, and
`hp_listing_attribute`; `hp_listing_category` is hierarchical. Listing vendor and
user relations are `post_parent` and `post_author`. Attribute settings use the
`hp_edit_field_*`, `hp_search_field_*`, and display/behavior metadata registered by
HivePress. Non-select values use `hp_{slug}`; select values are term relationships
in `hp_listing_{normalized_slug}`. Listing images are attachment children marked
with `hp_parent_field=images`. Geolocation contributes `hp_location`,
`hp_latitude`, and `hp_longitude`. This installation has no marketplace price
extension; price is available only when a `price` listing attribute exists.

=== Limitations ===

There is intentionally no permanent delete, arbitrary meta, general user CRUD, or
password API. Creating a vendor-specific custom attribute payload is reserved for
a future schema endpoint; the `attributes` key is accepted for forward compatibility
but ignored rather than written as arbitrary metadata. New routes must be deployed
to the running WordPress installation before they can be exercised remotely.
