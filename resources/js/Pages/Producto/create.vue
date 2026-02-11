<script setup lang="ts">
import { reactive, ref, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import FormField from '../../components/FormField.vue'

const state = reactive({
  categoriaId: '',
  codigo: '',
  nombre: '',
  descripcion: '',
  precioUnitario: 0,
  stock: 0,
  tipo: 'bien',
  activo: true
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

const tipos = [
  { label: 'Bien', value: 'bien' },
  { label: 'Servicio', value: 'servicio' }
]

const estadosActivo = [
  { label: 'Activo', value: true },
  { label: 'Inactivo', value: false }
]

const handleSubmit = () => {
  isLoading.value = true

  router.post(route('productos.store'), state, {
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
  router.visit(route('productos.index'))
}

const generarCodigoSugerido = () => {
  const timestamp = Date.now().toString().slice(-6)
  state.codigo = `PROD-${timestamp}`
}
</script>

<template>
  <UDashboardPanel>
    <template #header>
      <UDashboardNavbar title="Crear Producto">
        <template #leading>
          <UDashboardSidebarCollapse />
        </template>
      </UDashboardNavbar>
    </template>

    <template #body>
      <div class="p-6">
        <div class="mb-6">
          <h2 class="text-2xl font-semibold">Nuevo Producto</h2>
          <p class="text-sm text-muted mt-1">Complete los datos del producto para el inventario</p>
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-6">
          <div class="grid grid-cols-2 gap-8">
            <FormField label="Código" name="codigo" required :error="errors.codigo">
              <div class="flex gap-2">
                <UInput
                  v-model="state.codigo"
                  placeholder="PROD-XXXX"
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
                placeholder="Nombre del producto"
                icon="i-lucide-tag"
                size="xl"
                class="w-full"
              />
            </FormField>
          </div>

          <FormField label="Descripción" name="descripcion" :error="errors.descripcion">
            <UTextarea
              v-model="state.descripcion"
              placeholder="Descripción detallada del producto (opcional)"
              size="xl"
              :rows="3"
              class="w-full"
            />
          </FormField>

          <div class="grid grid-cols-2 gap-8">
            <FormField label="Categoría" name="categoriaId" required :error="errors.categoria_id || errors.categoriaId">
              <UInput
                v-model="state.categoriaId"
                placeholder="ID de categoría"
                icon="i-lucide-folder"
                size="xl"
                class="w-full"
              />
            </FormField>

            <FormField label="Tipo" name="tipo" required :error="errors.tipo">
              <USelect
                v-model="state.tipo"
                :items="tipos"
                placeholder="Seleccione tipo"
                size="xl"
                class="w-full"
              />
            </FormField>
          </div>

          <div class="grid grid-cols-2 gap-8">
            <FormField label="Precio Unitario" name="precioUnitario" required :error="errors.precio_unitario || errors.precioUnitario">
              <UInput
                v-model.number="state.precioUnitario"
                type="number"
                step="0.01"
                min="0"
                placeholder="0.00"
                icon="i-lucide-dollar-sign"
                size="xl"
                class="w-full"
              />
            </FormField>

            <FormField label="Stock" name="stock" required :error="errors.stock">
              <UInput
                v-model.number="state.stock"
                type="number"
                min="0"
                placeholder="0"
                icon="i-lucide-package"
                size="xl"
                class="w-full"
              />
            </FormField>
          </div>

          <FormField label="Estado" name="activo" required :error="errors.activo">
            <USelect
              v-model="state.activo"
              :items="estadosActivo"
              placeholder="Seleccione estado"
              size="xl"
              class="w-full"
            />
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
              label="Guardar Producto"
              icon="i-lucide-save"
              :loading="isLoading"
            />
          </div>
        </form>
      </div>
    </template>
  </UDashboardPanel>
</template>
