<template>
    <UDashboardSidebar collapsible resizable>
        <template #header="{ collapsed }">
            <Link class="mx-auto text-center" href="/">
                <h1 v-if="!collapsed" class="front-semibold text-xl">CMS</h1>
                <UIcon v-else class="mx-auto size-5 text-primary" name="i-lucide-brick-wall" />
            </Link>
        </template>

        <template #default="{ collapsed }">
            <UNavigationMenu :collapsed="collapsed" :items="navigation" orientation="vertical" />
        </template>

        <template #footer="{ collapsed }">
            <ExtensionPoint :extension-props="{ collapsed }" name="navigation.sidebar.footer" />
        </template>
    </UDashboardSidebar>
</template>

<script lang="ts" setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import ExtensionPoint from '@modules/Module/Components/ExtensionPoint.vue';

const page = usePage();
const navigation = computed(() => page.props.navigation);
</script>
