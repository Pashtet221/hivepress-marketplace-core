=== Codex WordPress Bridge ===
Contributors: wpdevstudio
Tags: rest-api, acf, codex, content-management
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 0.6.0
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
