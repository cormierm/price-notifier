<template>
    <TransitionRoot appear :show="isOpen" as="template">
        <Dialog as="div" @close="closeModal" class="relative z-10">
            <TransitionChild
                as="template"
                enter="duration-300 ease-out"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="duration-200 ease-in"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div class="fixed inset-0 bg-black/25" />
            </TransitionChild>

            <div class="fixed inset-0 overflow-y-auto">
                <div
                    class="flex min-h-full items-center justify-center p-4 text-center"
                >
                    <TransitionChild
                        as="template"
                        enter="duration-300 ease-out"
                        enter-from="opacity-0 scale-95"
                        enter-to="opacity-100 scale-100"
                        leave="duration-200 ease-in"
                        leave-from="opacity-100 scale-100"
                        leave-to="opacity-0 scale-95"
                    >
                        <DialogPanel
                            class="w-full max-w-xl transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all"
                        >
                            <DialogTitle as="h3" class="text-xl font-medium leading-6 text-gray-900">
                                Delete Watcher
                            </DialogTitle>
                            <div class="mt-2">
                                <p class="text-gray-500 flex flex-col">
                                    <span>Are your sure you want to delete this watcher?</span>
                                    <span class="text-black text-md font-bold">{{ watcher.name}}</span>
                                    <span>This action CANNOT be undone.</span>
                                </p>

                            </div>

                            <div class="mt-4 flex justify-between">
                                <button
                                    type="button"
                                    class="rounded-md border hover:border-gray-300 px-4 py-2 text-sm font-medium"
                                    @click="closeModal"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="button"
                                    class="rounded-md bg-red-500 hover:bg-red-600 px-4 py-2 text-sm font-medium text-white"
                                    @click="emit('confirmed')"
                                >
                                    Confirm
                                </button>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script setup>
import {Dialog, DialogPanel, DialogTitle, TransitionRoot, TransitionChild} from '@headlessui/vue'

const emit = defineEmits(['confirmed'])

const props = defineProps({
    watcher: {
        type: Object,
        required: true
    }
})
const isOpen = defineModel('isOpen', {type: Boolean, default: false});

function closeModal() {
    isOpen.value = false
}
</script>
