import BlockRegistry from './blockRegistry';

BlockRegistry.register('text', () => import('./Blocks/TextBlock.vue'));
BlockRegistry.register('image', () => import('./Blocks/ImageBlock.vue'));
BlockRegistry.register('column', () => import('./Blocks/ColumnBlock.vue'));
BlockRegistry.register('row', () => import('./Blocks/RowBlock.vue'));

BlockRegistry.register('page', () => import('./Blocks/PageBlock.vue'));

BlockRegistry.register('spacer', () => import('./Blocks/SpacerBlock.vue'));

BlockRegistry.register('separator', () => import('./Blocks/SeparatorBlock.vue'));

BlockRegistry.register('button', () => import('./Blocks/ButtonBlock.vue'));
