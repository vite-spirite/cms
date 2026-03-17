# PageBuilder Module

The PageBuilder module provides a drag-and-drop page editor with a block-based content system. Pages can be published,
drafted, or archived and are rendered on the public frontend.

## Overview

- Visual drag-and-drop page editor
- Block-based content system, extensible by other modules
- Page status management (draft, published, archived)
- SEO meta tags (OG balises) per page
- Soft delete support
- Public page rendering via slug

## Routes

### Web

| Method | URI                         | Name                  | Middleware                |
|--------|-----------------------------|-----------------------|---------------------------|
| GET    | `/admin/page/list`          | `page.list`           | `auth`                    |
| GET    | `/admin/page/create`        | `page.create`         | `auth`, `can:page_create` |
| POST   | `/admin/page/create`        | `page.create.request` | `auth`, `can:page_create` |
| GET    | `/admin/page/edit/{page}`   | `page.edit`           | `auth`, `can:page_edit`   |
| PUT    | `/admin/page/edit/{page}`   | `page.edit.request`   | `auth`, `can:page_edit`   |
| DELETE | `/admin/page/delete/{page}` | `page.delete`         | `auth`, `can:page_delete` |
| GET    | `/{slug?}`                  | `page.render`         | —                         |

::: warning
The `page.render` route catches all unmatched URLs. Make sure it is the last route registered in your application.
:::

## Permissions

| Key           | Display Name | Description                    |
|---------------|--------------|--------------------------------|
| `page_create` | Create page  | Ability to create new pages    |
| `page_edit`   | Edit page    | Ability to edit existing pages |
| `page_delete` | Delete page  | Ability to delete pages        |

## Page model

| Field          | Type        | Description                                          |
|----------------|-------------|------------------------------------------------------|
| `title`        | `string`    | Page title                                           |
| `slug`         | `string`    | Unique URL slug                                      |
| `content`      | `array`     | Serialized block tree                                |
| `status`       | `enum`      | `draft`, `published`, `archived`                     |
| `og_balises`   | `array`     | SEO meta tags as key/value pairs                     |
| `created_by`   | `foreignId` | User who created the page                            |
| `updated_by`   | `foreignId` | User who last updated the page                       |
| `published_at` | `timestamp` | Set automatically when status changes to `published` |

## Block system

A block is the smallest unit of content in the PageBuilder. Each block has a **PHP class** that defines its schema and a
**Vue component** that renders it.

### Built-in blocks

| Type     | Label          | Description                                                   |
|----------|----------------|---------------------------------------------------------------|
| `page`   | Page container | Root container with background color, text color, and padding |
| `row`    | Row            | Horizontal container with configurable gap                    |
| `column` | Column         | Vertical container with configurable gap                      |
| `text`   | Rich text      | Rich text editor with Markdown output                         |
| `image`  | Image          | Image with URL and alt text                                   |

### Adding blocks from another module

See [Creating a Block](/modules/creating-block) for a full step-by-step guide.

In short, register your block class in your module's service provider:

```php
ModuleHelper::when('PageBuilder', function () {
    $registry = $this->app->make(\App\Modules\PageBuilder\Services\BlockRegistry::class);
    $registry->register(MyBlock::class);
});
```

And register the Vue component in your module's `blocks.ts`:

```ts
import BlockRegistry from '@modules/PageBuilder/blockRegistry';

BlockRegistry.register('my-block', () => import('./Blocks/MyBlock.vue'));
```

## BlockRegistry (PHP)

The `BlockRegistry` singleton manages all registered block classes on the PHP side.

| Method                                         | Description                                               |
|------------------------------------------------|-----------------------------------------------------------|
| `register(string $blockClass)`                 | Registers a single block class                            |
| `registerMany(array $blockClasses)`            | Registers multiple block classes                          |
| `has(string $type): bool`                      | Checks if a block type is registered                      |
| `definitions(): array`                         | Returns all block definitions (type, label, icon, schema) |
| `serialize(array $blocks): array`              | Cleans and serializes a block tree for storage            |
| `render(array $blocks): array`                 | Prepares a block tree for frontend rendering              |
| `resolveInstance(array $block): AbstractBlock` | Instantiates a block from raw data                        |

## Frontend stores

The PageBuilder frontend uses a Pinia store (`usePageBuilderStore`) to manage the editor state.

### Key state

| Property        | Type                  | Description                            |
|-----------------|-----------------------|----------------------------------------|
| `blocks`        | `PageBlock[]`         | The current block tree                 |
| `settings`      | `PageBuilderSettings` | Page title, slug, status, og_balises   |
| `selectedBlock` | `PageBlock \| null`   | Currently selected block in the editor |
| `hoveredBlock`  | `PageBlock \| null`   | Currently hovered block                |

### Key actions

| Action                        | Description                                           |
|-------------------------------|-------------------------------------------------------|
| `addBlock(definition)`        | Adds a block to the tree or to the selected container |
| `removeById(id)`              | Removes a block by ID anywhere in the tree            |
| `findBlockById(id)`           | Finds a block by ID recursively                       |
| `duplicateBlock(block)`       | Deep-clones a block with new IDs                      |
| `selectBlock(block)`          | Sets the selected block                               |
| `selectParentBlock(block)`    | Selects the parent of the given block                 |
| `hydrate(page, definitions?)` | Loads a page into the editor                          |
| `serialize()`                 | Returns the current state ready for form submission   |
| `reset()`                     | Resets the store to its initial state                 |

## Navigation

```
Page builder
├── List   →  page.list
└── Create →  page.create
```
