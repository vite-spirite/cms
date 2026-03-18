# Creating a Block

Blocks are the building units of the PageBuilder. Each block has a PHP class that defines its schema and a Vue component
that renders it. This page walks through creating a block from scratch.

## Overview

A block is composed of:

- A **PHP class** extending `AbstractBlock` — defines the type, schema, and serialization logic
- A **Vue component** — renders the block in the page builder and on the public page
- A **registration** on both sides (PHP service provider + `blocks.ts`)

## Step 1: Create the PHP class

Create a new class in your module extending `AbstractBlock`:

```php
<?php

namespace App\Modules\MyModule\Blocks;

use App\Modules\PageBuilder\Contracts\AbstractBlock;

class QuoteBlock extends AbstractBlock
{
    public static function type(): string
    {
        return 'quote'; // unique identifier, used to match the Vue component
    }

    public static function label(): string
    {
        return 'Quote'; // displayed in the block picker
    }

    public static function icon(): string
    {
        return 'i-lucide-quote'; // Iconify icon
    }

    public static function schema(): array
    {
        return [
            'content' => [
                'label'    => 'Quote content',
                'type'     => 'richtext',
                'default'  => 'Your quote here...',
                'required' => true,
            ],
            'author' => [
                'label'   => 'Author',
                'type'    => 'text',
                'default' => '',
            ],
            'align' => [
                'label'   => 'Alignment',
                'type'    => 'select',
                'default' => 'left',
                'options' => ['left', 'center', 'right'],
            ],
        ];
    }
}
```

### Schema field types

| Type       | Component       | Description                                         |
|------------|-----------------|-----------------------------------------------------|
| `text`     | `TextInput`     | Single line text                                    |
| `richtext` | `RichTextInput` | Rich text editor (Markdown)                         |
| `select`   | `SelectInput`   | Dropdown, requires `options` array                  |
| `int`      | `NumberInput`   | Numeric input                                       |
| `color`    | `ColorInput`    | Color picker                                        |
| `blocks`   | —               | Nested blocks (children), used for container blocks |
| `media`    | `MediaField`    | Media picker (requires Gallery module)              |

### Schema field properties

| Property   | Type     | Required | Description                                |
|------------|----------|----------|--------------------------------------------|
| `label`    | `string` | yes      | Label displayed in the settings panel      |
| `type`     | `string` | yes      | Field type (see table above)               |
| `default`  | `mixed`  | no       | Default value when the block is created    |
| `required` | `bool`   | no       | Throws if value is empty during validation |
| `options`  | `array`  | no       | Only for `select` type                     |

## The `data()` method

The `data()` method allows a block to enrich its data with **dynamic PHP-side content** at render time. It is called by
`toRenderArray()` when the BlockRegistry renders a page for the frontend — it is **not** called during serialization (
storage).

This is the right place to fetch related models, resolve a slug to its content, or inject any server-side data that
cannot be stored in the block's schema fields.

```php
public function data(): array
{
    return [];
}
```

By default it returns an empty array. Override it to merge additional data into the block's props before they are sent
to the Vue component.

### Example: injecting article content from a slug

```php
<?php

namespace App\Modules\Blog\Blocks;

use App\Modules\Blog\Models\Post;
use App\Modules\PageBuilder\Contracts\AbstractBlock;

class ArticleBlock extends AbstractBlock
{
    public static function type(): string
    {
        return 'article';
    }

    public static function label(): string
    {
        return 'Article';
    }

    public static function icon(): string
    {
        return 'i-lucide-newspaper';
    }

    public static function schema(): array
    {
        return [
            'slug' => [
                'label'    => 'Article slug',
                'type'     => 'text',
                'default'  => '',
                'required' => true,
            ],
        ];
    }

    public function data(): array
    {
        $post = Post::where('slug', $this->get('slug'))->first();

        if (!$post) {
            return ['post' => null];
        }

        return [
            'post' => [
                'title'   => $post->title,
                'content' => $post->content,
                'author'  => $post->author->name,
            ],
        ];
    }
}
```

The Vue component then receives both the schema fields (`slug`) **and** the dynamic data (`post`) as props:

```vue

<template>
    <div v-if="post">
        <h2>{{ post.title }}</h2>
        <p>{{ post.author }}</p>
        <div v-html="post.content"/>
    </div>
    <div v-else>Article not found.</div>
</template>

<script lang="ts" setup>
    defineProps<{
        id: string
        slug: string
        editable: boolean
        selected: boolean
        post: { title: string; content: string; author: string } | null
    }>()
</script>
```

::: warning
`data()` is only called during **rendering** (`BlockRegistry::render()`), not during serialization (
`BlockRegistry::serialize()`). The dynamic data is never stored in the database — only the schema fields are persisted.
This means `data()` runs on every page load.
:::

::: tip
Use `$this->get('key', $default)` to safely read schema field values inside `data()`. This is equivalent to
`$this->data['key'] ?? $default`.
:::

## Step 2: Register the PHP class

Register your block in your module's service provider. The registration must happen after all providers are booted to
ensure the `BlockRegistry` singleton is available:

```php
<?php

namespace App\Modules\MyModule\Providers;

use App\Core\Module\BaseModuleServiceProvider;
use App\Modules\MyModule\Blocks\QuoteBlock;
use App\Modules\PageBuilder\Services\BlockRegistry;

class MyModuleServiceProvider extends BaseModuleServiceProvider
{
    public function boot(): void
    {
        parent::boot();

        ModuleHelper::when('PageBuilder', function () {
            $registry = $this->app->make(BlockRegistry::class);
            $registry->register(QuoteBlock::class);
        });
    }
}
```

::: tip
Always wrap your block registration in `ModuleHelper::when('PageBuilder')` so your module does not break if PageBuilder
is disabled.
:::

## Step 3: Create the Vue component

Create a Vue component in your module's `Resources/js/Blocks/` directory. The component receives all schema fields as
props, plus `id`, `editable`, and `selected`.

```vue
<!-- app/Modules/MyModule/Resources/js/Blocks/QuoteBlock.vue -->
<template>
    <blockquote :style="{ textAlign: align }" class="border-l-4 border-primary pl-4 italic">
        <div v-html="content"/>
        <footer v-if="author" class="mt-2 text-sm font-semibold">— {{ author }}</footer>
    </blockquote>
</template>

<script lang="ts" setup>
    const {id, content, author, align, editable, selected} = defineProps<{
        id: string
        content: string
        author: string
        align: string
        editable: boolean
        selected: boolean
    }>()
</script>
```

### Props every block receives

| Prop               | Type      | Description                                      |
|--------------------|-----------|--------------------------------------------------|
| `id`               | `string`  | Unique block ID                                  |
| `editable`         | `boolean` | `true` in the builder, `false` on public render  |
| `selected`         | `boolean` | `true` when the block is selected in the builder |
| `...schema fields` | `mixed`   | All fields defined in your PHP schema            |

### Container blocks

If your block has a `children` field in its schema, it acts as a container and receives a default slot with
`containerClass` and `containerStyle`:

```vue

<template>
    <div>
        <slot :container-class="classes" :container-style="styles"/>
    </div>
</template>

<script lang="ts" setup>
    import {computed} from 'vue';

    const {space_y, children} = defineProps<{
        id: string
        space_y: number
        children: any[]
        editable: boolean
        selected: boolean
    }>()

    const classes = computed(() => 'flex flex-col w-full');
    const styles = computed(() => ({gap: `${space_y}px`}));
</script>
```

::: warning
For container blocks, always expose the slot with `containerClass` and `containerStyle` — the `BlockRender` component
relies on these to inject child blocks and the drag-and-drop sortable list.
:::

## Step 4: Register the Vue component

Add the block registration to your module's `blocks.ts`:

```ts
// app/Modules/MyModule/Resources/js/blocks.ts
import BlockRegistry from '@modules/PageBuilder/blockRegistry';

BlockRegistry.register('quote', () => import('./Blocks/QuoteBlock.vue'));
```

The first argument must match the `type()` value of your PHP class.

## Step 5: Register a custom field (optional)

If your schema uses a custom field type not already available, create a field component and register it in `fields.ts`:

```vue
<!-- app/Modules/MyModule/Resources/js/Components/Fields/MyField.vue -->
<template>
    <UFormField :label="label" class="w-full">
        <!-- your custom input -->
    </UFormField>
</template>

<script lang="ts" setup>
    const model = defineModel<string>({required: true});
    const {label} = defineProps<{ label: string }>();
</script>
```

```ts
// app/Modules/MyModule/Resources/js/fields.ts
import FieldRegistry from '@modules/PageBuilder/fieldRegistry';

FieldRegistry.register('my-field', () => import('./Components/Fields/MyField.vue'));
```

## Full checklist

- PHP class extending `AbstractBlock` with `type()`, `label()`, `icon()`, `schema()`
- Override `data()` if the block needs dynamic server-side data at render time
- Block registered in service provider inside `ModuleHelper::when('PageBuilder')`
- Vue component in `Resources/js/Blocks/`
- Block registered in `Resources/js/blocks.ts`
- Custom field component + `fields.ts` registration (if needed)
