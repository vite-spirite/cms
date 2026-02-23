<template>
    <UDashboardPanel id="module_manage">
        <template #header>
            <UDashboardNavbar title="Manage modules">
                <template #leading>
                    <UDashboardSidebarCollapse />
                </template>
            </UDashboardNavbar>
        </template>

        <template #body>
            <UPage class="container mx-auto">
                <div class="grid gap-3">
                    <UAlert
                        v-if="!npm"
                        color="error"
                        description="Laravel does not have access to the “npm” command, which prevents the system from rebuilding user interfaces when modules are enabled/disabled."
                        icon="i-lucide-square-terminal"
                        title="NPM"
                        variant="soft"
                    />

                    <UCard v-for="moduleKey in Object.keys(modules)">
                        <div class="grid w-full gap-2">
                            <div class="flex items-center justify-between">
                                <h2 class="text-xl font-semibold">{{ modules[moduleKey].name }}</h2>
                                <USwitch
                                    v-if="gate.can('module_manage')"
                                    :default-value="moduleEnabled.includes(moduleKey)"
                                    :disabled="modules[moduleKey].type === 'core'"
                                    @update:modelValue="loadModule(moduleKey)"
                                />
                            </div>
                            <p>{{ modules[moduleKey].description }}</p>
                        </div>
                    </UCard>
                </div>
            </UPage>
        </template>
    </UDashboardPanel>
</template>

<script lang="ts" setup>
import Layout from '@/Layout/Dashboard.vue';
import { router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useApi } from '@modules/Module/Composables/useApi';
import { route } from 'ziggy-js';
import { useGate } from '@modules/Module/Composables/useGate';

const gate = useGate();

defineOptions({ layout: Layout });

type Props = {
    npmAvailable: boolean;
    modules: Record<string, any>;
    moduleEnabled: string[];
};

const page = usePage<Props>();
const npm = computed(() => page.props.npmAvailable);
const modules = computed(() => page.props.modules);
const moduleEnabled = computed(() => page.props.moduleEnabled);

const api = useApi();

const loadModule = async (module: string) => {
    router.get(route('admin.module.toggle', { module }), {}, { preserveState: false, async: true, showProgress: true });
};
</script>
