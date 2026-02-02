<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

const form = ref({
    numeroOrden: '',
    numeroDocumento: ''
})

const status = ref<'idle' | 'loading'>('idle')

const handleSearch = () => {
    status.value = 'loading'
    router.post(route('consulta.buscar'), form.value, {
        onFinish: () => {
            status.value = 'idle'
        }
    })
}
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-primary-50 to-gray-100 flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    Consulta de Reparación
                </h1>
                <p class="text-gray-600">
                    Verifique el estado de su equipo
                </p>
            </div>

            <UCard class="shadow-xl">
                <form @submit.prevent="handleSearch" class="space-y-6">
                    <UFormGroup label="Número de Orden" required>
                        <UInput
                            v-model="form.numeroOrden"
                            placeholder="Ej: ORD-2026-0001"
                            icon="i-lucide-file-text"
                            size="xl"
                            required
                        />
                    </UFormGroup>

                    <UFormGroup label="Número de Documento" required>
                        <UInput
                            v-model="form.numeroDocumento"
                            placeholder="Ej: 12345678"
                            icon="i-lucide-id-card"
                            size="xl"
                            required
                        />
                        <template #hint>
                            <span class="text-xs text-gray-500">
                                Ingrese su DNI, RUC o pasaporte registrado
                            </span>
                        </template>
                    </UFormGroup>

                    <UButton
                        type="submit"
                        color="primary"
                        size="xl"
                        block
                        :loading="status === 'loading'"
                        :disabled="!form.numeroOrden || !form.numeroDocumento"
                    >
                        <template #leading>
                            <UIcon name="i-lucide-search" />
                        </template>
                        Consultar Estado
                    </UButton>
                </form>

                <template #footer>
                    <div class="text-center text-sm text-gray-500">
                        <p>¿No tiene su número de orden?</p>
                        <p class="mt-1">
                            <a href="mailto:soporte@ejemplo.com" class="text-primary-600 hover:underline">
                                Contáctenos
                            </a>
                        </p>
                    </div>
                </template>
            </UCard>

            <p class="text-center text-xs text-gray-400 mt-8">
                © 2026 Sistema de Gestión de Reparaciones
            </p>
        </div>
    </div>
</template>
