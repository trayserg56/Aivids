<script setup>
import { useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useContactModal } from '@/composables/useContactModal';
import { formatPhoneInput, isValidPhone, normalizePhone, PHONE_ERROR } from '@/utils/phone';

const props = defineProps({
    fieldIdPrefix: { type: String, default: 'contact' },
    sourceSection: { type: String, default: null },
    sourceLabel: { type: String, default: null },
});

const { context } = useContactModal();

const successText = 'Заявка отправлена! Мы свяжемся с вами в ближайшее время.';

const form = useForm({
    name: '',
    email: '',
    phone: '',
    message: '',
    source_section: '',
    source_label: '',
});

const submitted = ref(false);
const phoneTouched = ref(false);

const successMessage = computed(() => (submitted.value ? successText : null));

const phoneInputClass = computed(() => [
    'w-full rounded-xl border bg-bg px-4 py-3 text-white outline-none',
    form.errors.phone
        ? 'border-red-500 focus:border-red-500'
        : 'border-border focus:border-accent',
]);

function fieldId(name) {
    return `${props.fieldIdPrefix}-${name}`;
}

function resolveSource() {
    return {
        source_section: props.sourceSection ?? context.value.section ?? '',
        source_label: props.sourceLabel ?? context.value.label ?? '',
    };
}

function validatePhone() {
    const phone = form.phone.trim();

    if (!phone) {
        form.setError('phone', 'Укажите номер телефона для связи.');

        return false;
    }

    if (!isValidPhone(phone)) {
        form.setError('phone', PHONE_ERROR);

        return false;
    }

    form.clearErrors('phone');

    return true;
}

function handlePhoneInput(event) {
    form.phone = formatPhoneInput(event.target.value);

    if (phoneTouched.value) {
        validatePhone();
    }
}

function handlePhoneBlur() {
    phoneTouched.value = true;
    validatePhone();
}

function submit() {
    phoneTouched.value = true;

    if (!form.name.trim()) {
        form.setError('name', 'Укажите, как к вам обращаться.');
    } else {
        form.clearErrors('name');
    }

    if (!validatePhone()) {
        return;
    }

    if (!form.name.trim()) {
        return;
    }

    const source = resolveSource();

    form
        .transform((data) => ({
            ...data,
            phone: normalizePhone(data.phone),
            message: data.message?.trim() ?? '',
            source_section: source.source_section || null,
            source_label: source.source_label || null,
        }))
        .post('/contact', {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
                phoneTouched.value = false;
                submitted.value = true;
            },
        });
}

function reset() {
    form.reset();
    form.clearErrors();
    phoneTouched.value = false;
    submitted.value = false;
}

defineExpose({ reset });
</script>

<template>
    <form class="space-y-5" @submit.prevent="submit">
        <div
            v-if="successMessage"
            class="rounded-xl border border-green-500/30 bg-green-500/10 px-4 py-3 text-sm text-green-300"
        >
            {{ successMessage }}
        </div>

        <div>
            <label :for="fieldId('name')" class="mb-2 block text-sm font-medium text-white">Имя</label>
            <input
                :id="fieldId('name')"
                v-model="form.name"
                type="text"
                class="w-full rounded-xl border border-border bg-bg px-4 py-3 text-white outline-none focus:border-accent"
                placeholder="Как к вам обращаться"
            />
            <p v-if="form.errors.name" class="mt-1 text-sm text-red-400">{{ form.errors.name }}</p>
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label :for="fieldId('email')" class="mb-2 block text-sm font-medium text-white">Email</label>
                <input
                    :id="fieldId('email')"
                    v-model="form.email"
                    type="email"
                    class="w-full rounded-xl border border-border bg-bg px-4 py-3 text-white outline-none focus:border-accent"
                    placeholder="you@company.ru"
                />
                <p v-if="form.errors.email" class="mt-1 text-sm text-red-400">{{ form.errors.email }}</p>
            </div>
            <div>
                <label :for="fieldId('phone')" class="mb-2 block text-sm font-medium text-white">Телефон</label>
                <input
                    :id="fieldId('phone')"
                    :value="form.phone"
                    type="tel"
                    inputmode="tel"
                    autocomplete="tel"
                    maxlength="18"
                    :class="phoneInputClass"
                    placeholder="+7 (999) 123-45-67"
                    @input="handlePhoneInput"
                    @blur="handlePhoneBlur"
                />
                <p v-if="form.errors.phone" class="mt-1 text-sm text-red-400">{{ form.errors.phone }}</p>
            </div>
        </div>

        <div>
            <label :for="fieldId('message')" class="mb-2 block text-sm font-medium text-white">О проекте</label>
            <textarea
                :id="fieldId('message')"
                v-model="form.message"
                rows="5"
                class="w-full resize-y rounded-xl border border-border bg-bg px-4 py-3 text-white outline-none focus:border-accent"
                placeholder="Формат ролика, сроки, где будет использоваться..."
            />
            <p v-if="form.errors.message" class="mt-1 text-sm text-red-400">{{ form.errors.message }}</p>
        </div>

        <button type="submit" class="btn-primary w-full" :disabled="form.processing">
            {{ form.processing ? 'Отправляем...' : 'Отправить заявку' }}
        </button>
    </form>
</template>
