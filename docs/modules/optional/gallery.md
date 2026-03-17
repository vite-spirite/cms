# Gallery Module

The Gallery module provides media file upload and management. Uploaded files are stored on the public disk and can be
used in the PageBuilder via the `media` field type.

## Overview

- Multi-file upload with client-side and server-side validation
- Media library with uploader info and upload date
- Delete media with confirmation modal
- Integrates with PageBuilder via a `MediaField` and a `MediaImageBlock`
- API endpoint to list all media for use in other modules

## Routes

### Web

| Method | URI                       | Name              | Middleware                   |
|--------|---------------------------|-------------------|------------------------------|
| GET    | `/gallery/list`           | `gallery.list`    | `auth`                       |
| POST   | `/gallery/uploads`        | `gallery.uploads` | `auth`, `can:gallery_upload` |
| DELETE | `/gallery/delete/{media}` | `gallery.delete`  | `auth`, `can:gallery_delete` |

### API

| Method | URI                 | Name               | Middleware |
|--------|---------------------|--------------------|------------|
| GET    | `/api/gallery/list` | `api.gallery.list` | `auth`     |

## Permissions

| Key              | Display Name   | Description                   |
|------------------|----------------|-------------------------------|
| `gallery_upload` | Upload gallery | Ability to upload media files |
| `gallery_delete` | Delete gallery | Ability to delete media files |

## File validation

Files are validated on both the client and server side.

**Server-side (Laravel):**

```php
'files.*' => ['file', 'max:2048', 'mimes:jpg,jpeg,png,webp,gif']
```

**Accepted formats:** `jpg`, `jpeg`, `png`, `webp`, `gif`
**Maximum file size:** 2MB per file

## Media model

| Field         | Type        | Description                              |
|---------------|-------------|------------------------------------------|
| `label`       | `string`    | Original filename                        |
| `path`        | `string`    | Storage path relative to the public disk |
| `url`         | `string`    | Full public URL                          |
| `uploader_id` | `foreignId` | User who uploaded the file               |
| `created_at`  | `timestamp` | Upload date                              |

Files are stored under `storage/app/public/media/` and served via the `public` disk.

## PageBuilder integration

The Gallery module integrates with the PageBuilder when both modules are loaded.

### MediaImageBlock

A block that renders an image from the media library.

```php
// Schema
[
    'media' => ['type' => 'media', 'label' => 'Media', 'required' => true],
    'alt'   => ['type' => 'text',  'label' => 'Alt'],
]
```

Register in your `blocks.ts`:

```ts
// Registered automatically by the Gallery module
BlockRegistry.register('media-image', () => import('./Blocks/MediaImageBlock.vue'));
```

### MediaField

A custom field type for the block settings panel that opens a media picker modal to select an image from the library.

```ts
// Registered automatically by the Gallery module
FieldRegistry.register('media', () => import('./Components/MediaField.vue'));
```

Any block schema can use the `media` field type to let users pick an image from the gallery:

```php
public static function schema(): array
{
    return [
        'image' => [
            'label'    => 'Image',
            'type'     => 'media',
            'default'  => '',
            'required' => true,
        ],
    ];
}
```

## Navigation

```
Gallery  →  gallery.list
```
