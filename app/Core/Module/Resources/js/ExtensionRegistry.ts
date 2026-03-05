import type { Component } from 'vue';

let registry: Record<string, Component[]> = {};

export default {
    register(pointName: string, component: Component) {
        if (!registry[pointName]) {
            registry[pointName] = [];
        }

        registry[pointName].push(component);
    },
    resolve(pointName: string) {
        return registry[pointName] || [];
    },
    reset() {
        registry = {};
    },
};
