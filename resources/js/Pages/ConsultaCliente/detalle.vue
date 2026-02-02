<script setup lang="ts">
import { ref, computed, resolveComponent } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

const UBadge = resolveComponent('UBadge')
const UAlert = resolveComponent('UAlert')

const props = defineProps<{
    orden: {
        id: string
        numeroOrden: string
        estado: string
        estadoLabel: string
        estadoColor: string
        cliente: {
            razonSocial: string
            email: string
            telefono: string
        }
        equipo: {
            marca: string
            modelo: string
            imei: string
            color: string
        }
        fechaIngreso: string
        fechaPromesa?: string
        fechaEntrega?: string
        fallaReportada: string
        diagnosticoTecnico?: string
        solucionAplicada?: string
        costoManoObra: number
        costoRepuestos: number
        costoTotal: number
        adelanto?: number
        repuestos?: Array<{
            nombre: string
            cantidad: number
            precioUnitario: number
            subtotal: number
        }>
        observaciones?: string
        requiereAprobacion: boolean
        aprobadoPorCliente?: boolean
    }
}>()

const numeroDocumento = ref('')
const showRechazoModal = ref(false)
const motivoRechazo = ref('')
const isSubmitting = ref(false)

const puedeAprobar = computed(() => props.orden.estado === 'pendiente_aprobacion')

const handleAprobar = () => {
    if (!numeroDocumento.value) {
        alert('Ingrese su número de documento para verificar su identidad')
        return
    }
    isSubmitting.value = true
    router.post(route('consulta.aprobar', props.orden.id), {
        numeroDocumento: numeroDocumento.value
    }, {
        onFinish: () => { isSubmitting.value = false }
    })
}

const handleRechazar = () => {
    if (!numeroDocumento.value || motivoRechazo.value.length < 10) {
        alert('Complete todos los campos correctamente')
        return
    }
    isSubmitting.value = true
    router.post(route('consulta.rechazar', props.orden.id), {
        numeroDocumento: numeroDocumento.value,
        motivoRechazo: motivoRechazo.value
    }, {
        onFinish: () => {
            isSubmitting.value = false
            showRechazoModal.value = false
        }
    })
}
</script>

<template>
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-4xl mx-auto px-4">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        Orden de Reparación
                    </h1>
                    <p class="text-lg text-gray-600">
                        {{ orden.numeroOrden }}
                    </p>
                </div>

                <UBadge
                    :color="orden.estadoColor"
                    size="lg"
                    variant="solid"
                    class="text-lg"
                >
                    {{ orden.estadoLabel }}
                </UBadge>
            </div>

            <UAlert
                v-if="puedeAprobar"
                color="yellow"
                icon="i-lucide-alert-triangle"
                class="mb-6"
                title="¡Reparación lista para aprobar!"
            />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <UCard>
                    <template #header>
                        <h3 class="font-semibold text-gray-900">Cliente</h3>
                    </template>
                    <div class="space-y-2">
                        <p class="font-medium">{{ orden.cliente.razonSocial }}</p>
                        <p class="text-sm text-gray-600">
                            <UIcon name="i-lucide-mail" class="inline w-4 h-4 mr-1" />
                            {{ orden.cliente.email }}
                        </p>
                        <p class="text-sm text-gray-600">
                            <UIcon name="i-lucide-phone" class="inline w-4 h-4 mr-1" />
                            {{ orden.cliente.telefono }}
                        </p>
                    </div>
                </UCard>

                <UCard>
                    <template #header>
                        <h3 class="font-semibold text-gray-900">Equipo</h3>
                    </template>
                    <div class="space-y-2">
                        <p class="font-medium">{{ orden.equipo.marca }} {{ orden.equipo.modelo }}</p>
                        <p class="text-sm text-gray-600">Color: {{ orden.equipo.color }}</p>
                        <p class="text-sm text-gray-500 font-mono">IMEI: {{ orden.equipo.imei }}</p>
                    </div>
                </UCard>
            </div>

            <UCard class="mb-6">
                <template #header>
                    <h3 class="font-semibold text-gray-900">Detalles de la Reparación</h3>
                </template>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pb-4 border-b">
                        <div>
                            <p class="text-sm text-gray-500">Fecha de Ingreso</p>
                            <p class="font-medium">{{ orden.fechaIngreso }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Fecha Prometida</p>
                            <p class="font-medium">{{ orden.fechaPromesa || 'Por determinar' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Fecha de Entrega</p>
                            <p class="font-medium">{{ orden.fechaEntrega || 'Pendiente' }}</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500 mb-1">Falla Reportada</p>
                        <p class="bg-gray-50 p-3 rounded">{{ orden.fallaReportada }}</p>
                    </div>

                    <div v-if="orden.diagnosticoTecnico">
                        <p class="text-sm text-gray-500 mb-1">Diagnóstico Técnico</p>
                        <p class="bg-blue-50 p-3 rounded text-blue-900">{{ orden.diagnosticoTecnico }}</p>
                    </div>

                    <div v-if="orden.solucionAplicada">
                        <p class="text-sm text-gray-500 mb-1">Solución Aplicada</p>
                        <p class="bg-green-50 p-3 rounded text-green-900">{{ orden.solucionAplicada }}</p>
                    </div>
                </div>
            </UCard>

            <UCard v-if="orden.repuestos && orden.repuestos.length > 0" class="mb-6">
                <template #header>
                    <h3 class="font-semibold text-gray-900">Repuestos y Materiales</h3>
                </template>

                <UTable :data="orden.repuestos" :columns="[
                    { key: 'nombre', label: 'Descripción' },
                    { key: 'cantidad', label: 'Cant.' },
                    { key: 'precioUnitario', label: 'P. Unit.' },
                    { key: 'subtotal', label: 'Subtotal' }
                ]" />
            </UCard>

            <UCard class="mb-6">
                <template #header>
                    <h3 class="font-semibold text-gray-900">Resumen de Costos</h3>
                </template>

                <div class="space-y-2 text-right">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Mano de Obra:</span>
                        <span class="font-medium">${{ orden.costoManoObra?.toFixed(2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Repuestos:</span>
                        <span class="font-medium">${{ orden.costoRepuestos?.toFixed(2) }}</span>
                    </div>
                    <div v-if="orden.adelanto" class="flex justify-between text-green-600">
                        <span>Adelanto:</span>
                        <span class="font-medium">-${{ orden.adelanto?.toFixed(2) }}</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold pt-2 border-t">
                        <span>Total a Pagar:</span>
                        <span class="text-primary-600">${{ orden.costoTotal?.toFixed(2) }}</span>
                    </div>
                </div>
            </UCard>

            <UCard v-if="puedeAprobar" class="mb-6 border-2 border-yellow-400">
                <template #header>
                    <h3 class="font-semibold text-yellow-800">⚡ Acción Requerida</h3>
                </template>

                <UFormGroup label="Número de Documento" class="mb-4">
                    <UInput
                        v-model="numeroDocumento"
                        placeholder="Ingrese su DNI o RUC"
                        icon="i-lucide-id-card"
                        size="lg"
                    />
                </UFormGroup>

                <div class="flex gap-4">
                    <UButton
                        color="green"
                        size="lg"
                        variant="solid"
                        :loading="isSubmitting"
                        :disabled="!numeroDocumento"
                        @click="handleAprobar"
                        class="flex-1"
                    >
                        <UIcon name="i-lucide-check-circle" class="mr-2" />
                        Aprobar
                    </UButton>

                    <UButton
                        color="red"
                        size="lg"
                        variant="soft"
                        :disabled="!numeroDocumento"
                        @click="showRechazoModal = true"
                        class="flex-1"
                    >
                        <UIcon name="i-lucide-x-circle" class="mr-2" />
                        Rechazar
                    </UButton>
                </div>
            </UCard>

            <div class="text-center">
                <UButton
                    color="neutral"
                    variant="ghost"
                    @click="router.get(route('consulta.index'))"
                >
                    <UIcon name="i-lucide-arrow-left" class="mr-2" />
                    Consultar otra orden
                </UButton>
            </div>
        </div>

        <UModal v-model:open="showRechazoModal">
            <template #title>Rechazar Reparación</template>
            <template #description>
                Por favor indique el motivo del rechazo.
            </template>

            <UFormGroup label="Motivo" required>
                <UTextarea
                    v-model="motivoRechazo"
                    placeholder="Explique por qué rechaza (mínimo 10 caracteres)"
                    rows="4"
                    required
                />
            </UFormGroup>

            <template #footer>
                <div class="flex gap-2">
                    <UButton color="neutral" variant="soft" @click="showRechazoModal = false">
                        Cancelar
                    </UButton>
                    <UButton
                        color="red"
                        :loading="isSubmitting"
                        :disabled="motivoRechazo.length < 10"
                        @click="handleRechazar"
                    >
                        Confirmar Rechazo
                    </UButton>
                </div>
            </template>
        </UModal>
    </div>
</template>
