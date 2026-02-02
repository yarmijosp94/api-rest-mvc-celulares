<script setup lang="ts">
import { reactive, ref, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import FormField from '../../components/FormField.vue'

const state = reactive({
  codigo: '',
  nombre: '',
  descripcion: '',
  categoria: '',
  marcaCompatible: '',
  modeloCompatible: '',
  stockActual: 0,
  stockMinimo: 0,
  precioCompra: 0,
  precioVenta: 0,
  proveedor: '',
  ubicacion: '',
  estado: 'disponible'
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

const handleSubmit = () => {
  isLoading.value = true

  router.post(route('repuestos.store'), state, {
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

const generarCodigoSugerido = () => {
  const timestamp = Date.now().toString().slice(-6)
  state.codigo = `REP-${timestamp}`
}
</script>

<template>
  <UDashboardPanel>
    <template #header>
      <UDashboardNavbar title="Crear Repuesto">
        <template #leading>
          <UDashboardSidebarCollapse />
        </template>
      </UDashboardNavbar>
    </template>

    <template #body>
      <div class="p-6">
        <div class="mb-6">
          <h2 class="text-2xl font-semibold">Nuevo Repuesto</h2>
          <p class="text-sm text-muted mt-1">Complete los datos del repuesto para inventario</p>
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-6">
          <div class="grid grid-cols-2 gap-8">
            <FormField label="Código" name="codigo" required :error="errors.codigo">
              <div class="flex gap-2">
                <UInput
                  v-model="state.codigo"
                  placeholder="REP-XXXX"
                  icon="i-lucide-hash"
                  size="xl"
                  class="flex-1"
                />
                <UButton
                  type="button"
                  color="neutral"
                  variant="outline"
                  icon="i-lucide-sparkles"
                  @click="generarCodigoSugerido"
                />
              </div>
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
              label="Guardar Repuesto"
              icon="i-lucide-save"
              :loading="isLoading"
            />
          </div>
        </form>
      </div>
    </template>
  </UDashboardPanel>
</template>
