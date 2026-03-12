import BlockRegistry from '@modules/PageBuilder/blockRegistry';

BlockRegistry.register('media-image', () => import('./Blocks/MediaImageBlock.vue'));
