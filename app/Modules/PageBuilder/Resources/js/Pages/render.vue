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

const page = usePage<{ content: PageBlock[]; og_balises: Record<string, string>; title: string }>();
const content = computed(() => page.props.content);
const title = computed(() => page.props.title);
const ogBalises = computed(() => page.props.og_balises);

console.log(content.value);
</script>
