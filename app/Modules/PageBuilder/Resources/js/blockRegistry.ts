import type { AsyncComponentLoader} from 'vue';
import { defineAsyncComponent } from 'vue';

class BlockRegistry {
    private blocks: Record<string, AsyncComponentLoader> = {};

    public register(type: string, component: AsyncComponentLoader) {
        this.blocks[type] = defineAsyncComponent(component);
    }

    public registerMany(definitions: { type: string; loader: AsyncComponentLoader }[]) {
        definitions.forEach(({ type, loader }) => this.register(type, loader));
    }

    public resolve(type: string): AsyncComponentLoader {
        return this.blocks[type] ?? null;
    }

    public all(): Record<string, AsyncComponentLoader> {
        return this.blocks;
    }
}

export default new BlockRegistry();
