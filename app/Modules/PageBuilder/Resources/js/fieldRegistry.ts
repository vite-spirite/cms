import { type AsyncComponentLoader, defineAsyncComponent } from 'vue';

class FieldRegistry {
    private fields: Record<string, AsyncComponentLoader> = {};

    public register(type: string, component: AsyncComponentLoader) {
        this.fields[type] = defineAsyncComponent(component);
    }

    public registerMany(definitions: { type: string; component: AsyncComponentLoader }[]) {
        definitions.forEach((def) => this.register(def.type, def.component));
    }

    public resolve(type: string) {
        return this.fields[type] ?? null;
    }

    public all(): Record<string, AsyncComponentLoader> {
        return this.fields;
    }
}

export default new FieldRegistry();
