<script setup lang="ts">
import { reactive, ref, computed, resolveComponent } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import type { Repuesto } from '../../types'
import FormField from '../../components/FormField.vue'

const UBadge = resolveComponent('UBadge')

const props = defineProps<{
  repuesto: Repuesto
}>()

const state = reactive({
  codigo: props.repuesto.codigo,
  nombre: props.repuesto.nombre,
  descripcion: props.repuesto.descripcion || '',
  categoria: props.repuesto.categoria,
  marcaCompatible: props.repuesto.marcaCompatible || '',
  modeloCompatible: props.repuesto.modeloCompatible || '',
  stockActual: props.repuesto.stockActual,
  stockMinimo: props.repuesto.stockMinimo,
  precioCompra: props.repuesto.precioCompra,
  precioVenta: props.repuesto.precioVenta,
  proveedor: props.repuesto.proveedor || '',
  ubicacion: props.repuesto.ubicacion || '',
  estado: props.repuesto.estado
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

const categorias = [
  { label: 'Pantalla', value: 'Pantalla' },
  { label: 'Batería', value: 'Batería' },
  { label: 'Cámara', value: 'Cámara' },
  { label: 'Placa', value: 'Placa' },
  { label: 'Conector', value: 'Conector' },
  { label: 'Botón', value: 'Botón' },
  { label: 'Altavoz', value: 'Altavoz' },
  { label: 'Otros', value: 'Otros' }
]

const estados = [
  { label: 'Disponible', value: 'disponible' },
  { label: 'Agotado', value: 'agotado' },
  { label: 'Por Pedir', value: 'por_pedir' }
]

const margenGanancia = computed(() => {
  if (state.precioCompra > 0) {
    return ((state.precioVenta - state.precioCompra) / state.precioCompra * 100).toFixed(2)
  }
  return '0.00'
})

const handleSubmit = () => {
  isLoading.value = true

  router.put(route('repuestos.update', props.repuesto.id), state, {
    onFinish: () => {
      isLoading.value = false
    },
    onError: (errors) => {
      console.error('Errores de validación:', errors)
      isLoading.value = false
    }
  })
}

const handleCancel = () => {
  router.visit(route('repuestos.index'))
}
</script>

<template>
  <UDashboardPanel>
    <template #header>
      <UDashboardNavbar title="Editar Repuesto">
        <template #leading>
          <UDashboardSidebarCollapse />
        </template>
      </UDashboardNavbar>
    </template>

    <template #body>
      <div class="p-6">
        <div class="mb-6">
          <h2 class="text-2xl font-semibold">Editar Repuesto</h2>
          <p class="text-sm text-muted mt-1">Modifique los datos del repuesto</p>
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-6">
          <div class="grid grid-cols-2 gap-8">
            <FormField label="Código" name="codigo" required :error="errors.codigo">
              <UInput
                v-model="state.codigo"
                placeholder="REP-XXXX"
                icon="i-lucide-hash"
                size="xl"
                class="w-full"
                disabled
              />
            </FormField>

            <FormField label="Nombre" name="nombre" required :error="errors.nombre">
              <UInput
                v-model="state.nombre"
                placeholder="Nombre del repuesto"
                icon="i-lucide-tag"
                size="xl"
                class="w-full"
              />
            </FormField>
          </div>

          <FormField label="Descripción" name="descripcion" :error="errors.descripcion">
            <UTextarea
              v-model="state.descripcion"
              placeholder="Descripción detallada del repuesto (opcional)"
              size="xl"
              :rows="3"
              class="w-full"
            />
          </FormField>

          <div class="grid grid-cols-2 gap-8">
            <FormField label="Categoría" name="categoria" required :error="errors.categoria">
              <USelect
                v-model="state.categoria"
                :items="categorias"
                placeholder="Seleccione categoría"
                size="xl"
                class="w-full"
              />
            </FormField>

            <FormField label="Estado" name="estado" required :error="errors.estado">
              <USelect
                v-model="state.estado"
                :items="estados"
                placeholder="Seleccione estado"
                size="xl"
                class="w-full"
              />
            </FormField>
          </div>

          <div class="grid grid-cols-2 gap-8">
            <FormField label="Marca Compatible" name="marcaCompatible" :error="errors.marcaCompatible">
              <UInput
                v-model="state.marcaCompatible"
                placeholder="Ej: Samsung, Apple (opcional)"
                icon="i-lucide-smartphone"
                size="xl"
                class="w-full"
              />
            </FormField>

            <FormField label="Modelo Compatible" name="modeloCompatible" :error="errors.modeloCompatible">
              <UInput
                v-model="state.modeloCompatible"
                placeholder="Ej: Galaxy S21, iPhone 13 (opcional)"
                icon="i-lucide-layers"
                size="xl"
                class="w-full"
              />
            </FormField>
          </div>

          <div class="grid grid-cols-2 gap-8">
            <FormField label="Stock Actual" name="stockActual" required :error="errors.stockActual">
              <UInput
                v-model.number="state.stockActual"
                type="number"
                min="0"
                placeholder="0"
                icon="i-lucide-package"
                size="xl"
                class="w-full"
              />
            </FormField>

            <FormField label="Stock Mínimo" name="stockMinimo" required :error="errors.stockMinimo">
              <UInput
                v-model.number="state.stockMinimo"
                type="number"
                min="0"
                placeholder="0"
                icon="i-lucide-alert-triangle"
                size="xl"
                class="w-full"
              />
            </FormField>
          </div>

          <div class="grid grid-cols-2 gap-8">
            <FormField label="Precio de Compra" name="precioCompra" required :error="errors.precioCompra">
              <UInput
                v-model.number="state.precioCompra"
                type="number"
                step="0.01"
                min="0"
                placeholder="0.00"
                icon="i-lucide-dollar-sign"
                size="xl"
                class="w-full"
              />
            </FormField>

            <FormField label="Precio de Venta" name="precioVenta" required :error="errors.precioVenta">
              <UInput
                v-model.number="state.precioVenta"
                type="number"
                step="0.01"
                min="0"
                placeholder="0.00"
                icon="i-lucide-dollar-sign"
                size="xl"
                class="w-full"
              />
            </FormField>
          </div>

          <div class="bg-primary/10 border border-primary/20 rounded-lg p-4">
            <div class="flex items-center gap-2">
              <span class="text-sm font-medium">Margen de Ganancia:</span>
              <UBadge
                :color="parseFloat(margenGanancia) > 0 ? 'success' : 'error'"
                size="lg"
              >
                {{ margenGanancia }}%
              </UBadge>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-8">
            <FormField label="Proveedor" name="proveedor" :error="errors.proveedor">
              <UInput
                v-model="state.proveedor"
                placeholder="Nombre del proveedor (opcional)"
                icon="i-lucide-truck"
                size="xl"
                class="w-full"
              />
            </FormField>

            <FormField label="Ubicación" name="ubicacion" :error="errors.ubicacion">
              <UInput
                v-model="state.ubicacion"
                placeholder="Ej: Estante A3 (opcional)"
                icon="i-lucide-map-pin"
                size="xl"
                class="w-full"
              />
            </FormField>
          </div>

          <div class="bg-muted/50 rounded-lg p-4 text-sm">
            <p class="text-muted">
              <strong>ID:</strong> {{ repuesto.id }}
            </p>
            <p class="text-muted mt-1">
              <strong>Creado:</strong> {{ new Date(repuesto.createdAt).toLocaleString('es-ES') }}
            </p>
            <p class="text-muted mt-1">
              <strong>Actualizado:</strong> {{ new Date(repuesto.updatedAt).toLocaleString('es-ES') }}
            </p>
          </div>

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
              label="Actualizar Repuesto"
              icon="i-lucide-save"
              :loading="isLoading"
            />
          </div>
        </form>
      </div>
    </template>
  </UDashboardPanel>
</template>
