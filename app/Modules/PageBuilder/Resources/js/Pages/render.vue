<template>
    <Head>
        <title>{{ title }}</title>

        <meta v-for="(balise, idx) in ogBalises" :key="idx" :content="balise" :name="idx" />
    </Head>

    <div>
        <BlockRender v-for="block in content" :key="block.id" :block="block" :editable="false" />
    </div>
</template>

<script lang="ts" setup>
import { Head } from '@inertiajs/vue3';

import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import BlockRender from '../Components/BlockRender.vue';
import type { PageBlock } from '../types';
import { usePageBuilderStore } from '../Stores/usePageBuilderStore';

const page = usePage<{ content: PageBlock[]; og_balises: Record<string, string>; title: string; id: number; slug: string }>();
const content = computed(() => page.props.content);
const title = computed(() => page.props.title);
const ogBalises = computed(() => page.props.og_balises);

const store = usePageBuilderStore();
store.hydrate({
    title: title.value,
    og_balises: ogBalises.value,
    slug: page.props.slug,
    id: page.props.id,
    status: 'published',
    content: content.value,
    created_at: '',
    updated_at: '',
});
</script>
