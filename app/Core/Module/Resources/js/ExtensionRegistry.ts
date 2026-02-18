import { Component } from 'vue';

const registry: Record<string, Component[]> = {};

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
};
