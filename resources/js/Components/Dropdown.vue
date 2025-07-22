<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    align: {
        type: String,
        default: 'right', // Options: left, right, center
    },
    width: {
        type: String,
        default: '48', // Accepts any valid Tailwind width like '48', '56', '64', or even 'full'
    },
    offset: {
        type: String,
        default: 'mt-2', // Margin top offset
    },
    contentClasses: {
        type: String,
        default: 'py-1 bg-white',
    },
});

const open = ref(false);

const closeOnEscape = (e) => {
    if (open.value && e.key === 'Escape') open.value = false;
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const widthClass = computed(() => `w-${props.width}`);

const alignmentClasses = computed(() => {
    if (props.align === 'left') {
        return 'ltr:origin-top-left rtl:origin-top-right start-0';
    } else if (props.align === 'right') {
        return 'ltr:origin-top-right rtl:origin-top-left end-0';
    } else if (props.align === 'center') {
        return 'left-1/2 transform -translate-x-1/2 origin-top';
    } else {
        return 'origin-top';
    }
});
</script>

<template>
    <div class="relative">
        <div @click="open = !open" :aria-expanded="open.toString()" class="cursor-pointer">
            <slot name="trigger" />
        </div>

        <!-- Overlay -->
        <div
            v-show="open"
            class="fixed inset-0 z-40"
            @click="open = false"
        ></div>

        <!-- Dropdown Content -->
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-show="open"
                :class="[
                    'absolute z-50 rounded-md shadow-lg',
                    widthClass,
                    alignmentClasses,
                    props.offset
                ]"
                @click.self="open = false"
            >
                <div
                    class="rounded-md ring-1 ring-black ring-opacity-5"
                    :class="contentClasses"
                >
                    <slot name="content" />
                </div>
            </div>
        </Transition>
    </div>
</template>
