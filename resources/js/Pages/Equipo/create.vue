<script setup lang="ts">
import { reactive, ref, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import type { Cliente } from '../../types'
import FormField from '../../components/FormField.vue'

const props = defineProps<{
  clientes: Cliente[]
}>()

const state = reactive({
  clienteId: '',
  marca: '',
  modelo: '',
  imei: '',
  numeroSerie: '',
  color: '',
  patronBloqueo: '',
  observacionesIniciales: '',
  estadoFisico: 'Bueno',
  accesorios: {
    cargador: false,
    auriculares: false,
    caja: false,
    funda: false,
    mica_protectora: false
  }
})

const page = usePage()
const backendErrors = computed(() => page.props.errors || {})

const errors = computed(() => {
  const result: Record<string, string> = {}
  Object.keys(backendErrors.value).forEach(key => {
    const error = backendErrors.value[key]
    result[key] = Array.isArray(error) ? error[0] : error
  })
  return result
})

const isLoading = ref(false)

const clientesOptions = computed(() => {
  return props.clientes.map(cliente => ({
    label: cliente.razonSocial,
    value: cliente.id
  }))
})

const handleSubmit = () => {
  isLoading.value = true

  router.post(route('equipos.store'), state, {
    onSuccess: () => {
      // Redirect handled by Inertia
    },
    onFinish: () => {
      isLoading.value = false
    },
    onError: (errors) => {
      console.error('Validation errors:', errors)
      isLoading.value = false
    }
  })
}

const handleCancel = () => {
  router.visit(route('equipos.index'))
}
</script>

<template>
  <UDashboardPanel>
    <template #header>
      <UDashboardNavbar title="Crear Equipo">
        <template #leading>
          <UDashboardSidebarCollapse />
        </template>
      </UDashboardNavbar>
    </template>

    <template #body>
      <div class="p-6">
        <div class="mb-6">
          <h2 class="text-2xl font-semibold">Nuevo Equipo</h2>
          <p class="text-sm text-muted mt-1">Complete los datos del equipo móvil</p>
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-6">
          <div class="grid grid-cols-2 gap-8">
            <FormField label="Cliente" name="clienteId" required :error="errors.clienteId || errors.cliente_id">
              <USelect
                v-model="state.clienteId"
                :items="clientesOptions"
                placeholder="Seleccione el cliente"
                size="xl"
                class="w-full"
              />
            </FormField>

            <FormField label="IMEI" name="imei" required :error="errors.imei">
              <UInput
                v-model="state.imei"
                placeholder="Ingrese el IMEI del equipo"
                icon="i-lucide-hash"
                size="xl"
                class="w-full"
              />
            </FormField>
          </div>

          <div class="grid grid-cols-2 gap-8">
            <FormField label="Marca" name="marca" required :error="errors.marca">
              <UInput
                v-model="state.marca"
                placeholder="Ej: Samsung, Apple, Xiaomi"
                icon="i-lucide-smartphone"
                size="xl"
                class="w-full"
              />
            </FormField>

            <FormField label="Modelo" name="modelo" required :error="errors.modelo">
              <UInput
                v-model="state.modelo"
                placeholder="Ej: Galaxy S21, iPhone 13"
                icon="i-lucide-tag"
                size="xl"
                class="w-full"
              />
            </FormField>
          </div>

          <div class="grid grid-cols-2 gap-8">
            <FormField label="Número de Serie" name="numeroSerie" :error="errors.numeroSerie || errors.numero_serie">
              <UInput
                v-model="state.numeroSerie"
                placeholder="Número de serie (opcional)"
                icon="i-lucide-barcode"
                size="xl"
                class="w-full"
              />
            </FormField>

            <FormField label="Color" name="color" required :error="errors.color">
              <UInput
                v-model="state.color"
                placeholder="Ej: Negro, Blanco, Azul"
                icon="i-lucide-palette"
                size="xl"
                class="w-full"
              />
            </FormField>
          </div>

          <div class="grid grid-cols-2 gap-8">
            <FormField label="Estado Físico" name="estadoFisico" required :error="errors.estadoFisico || errors.estado_fisico">
              <USelect
                v-model="state.estadoFisico"
                :items="[
                  { label: 'Excelente', value: 'Excelente' },
                  { label: 'Bueno', value: 'Bueno' },
                  { label: 'Regular', value: 'Regular' },
                  { label: 'Malo', value: 'Malo' }
                ]"
                placeholder="Seleccione el estado físico"
                size="xl"
                class="w-full"
              />
            </FormField>

            <FormField label="Patrón de Bloqueo" name="patronBloqueo" :error="errors.patronBloqueo || errors.patron_bloqueo">
              <UInput
                v-model="state.patronBloqueo"
                placeholder="PIN, patrón o contraseña (opcional)"
                icon="i-lucide-lock"
                size="xl"
                class="w-full"
                type="password"
              />
            </FormField>
          </div>

          <FormField label="Observaciones Iniciales" name="observacionesIniciales" :error="errors.observacionesIniciales || errors.observaciones_iniciales">
            <UTextarea
              v-model="state.observacionesIniciales"
              placeholder="Describa el estado del equipo, daños visibles, etc."
              :rows="4"
              size="xl"
              class="w-full"
            />
          </FormField>

          <FormField label="Accesorios" name="accesorios">
            <div class="grid grid-cols-2 gap-4 mt-2">
              <UCheckbox
                v-model="state.accesorios.cargador"
                label="Cargador"
              />
              <UCheckbox
                v-model="state.accesorios.auriculares"
                label="Auriculares"
              />
              <UCheckbox
                v-model="state.accesorios.caja"
                label="Caja"
              />
              <UCheckbox
                v-model="state.accesorios.funda"
                label="Funda"
              />
              <UCheckbox
                v-model="state.accesorios.mica_protectora"
                label="Mica Protectora"
              />
            </div>
          </FormField>

          <div class="flex justify-end gap-3 pt-4">
            <UButton
              type="button"
              color="neutral"
              variant="outline"
              label="Cancelar"
              @click="handleCancel"
              :disabled="isLoading"
            />
            <UButton
              type="submit"
              color="primary"
              label="Guardar Equipo"
              icon="i-lucide-save"
              :loading="isLoading"
            />
          </div>
        </form>
      </div>
    </template>
  </UDashboardPanel>
</template>
