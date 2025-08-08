<script setup>
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
    show: Boolean,       // Controls visibility of the modal
    student: Object,     // Student data to edit
    classes: Array,      // Classes list
});

const emit = defineEmits(['close', 'updated']);

const form = useForm({
    first_name: '',
    last_name: '',
    class_id: '',
});

// Update form whenever student prop changes
watch(() => props.student, (newStudent) => {
    if (newStudent) {
        form.first_name = newStudent.first_name;
        form.last_name = newStudent.last_name;
        form.class_id = newStudent.class_id;
    }
}, { immediate: true });

const submit = () => {
    form.put(route('students.update', props.student.id), {
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Student updated successfully',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            }).then(() => {
                location.reload() // Refreshes the page
            });
        emit('updated');
        emit('close');
        }
    });
};
</script>

<template>
    <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg">
            <h2 class="text-xl font-semibold mb-4">Edit Student</h2>

            <form @submit.prevent="submit" class="space-y-4">
                <!-- First Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">First Name</label>
                    <input v-model="form.first_name" type="text" class="mt-1 block w-full rounded border-gray-300 shadow-sm" />
                    <div v-if="form.errors.first_name" class="text-red-600 text-sm mt-1">{{ form.errors.first_name }}</div>
                </div>

                <!-- Last Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Last Name</label>
                    <input v-model="form.last_name" type="text" class="mt-1 block w-full rounded border-gray-300 shadow-sm" />
                    <div v-if="form.errors.last_name" class="text-red-600 text-sm mt-1">{{ form.errors.last_name }}</div>
                </div>

                <!-- Class/Grade Level Dropdown -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Select Class / Grade Level</label>
                    <select v-model="form.class_id" class="mt-1 block w-full rounded border-gray-300 shadow-sm">
                        <option disabled value="">-- Choose Class --</option>
                        <option v-for="cls in classes" :key="cls.id" :value="cls.id">
                            {{ cls.name }} (Grade {{ cls.grade_level }})
                        </option>
                    </select>
                    <div v-if="form.errors.class_id" class="text-red-600 text-sm mt-1">{{ form.errors.class_id }}</div>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-2 pt-4">
                    <button
                        type="button"
                        @click="$emit('close')"
                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                    >
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
