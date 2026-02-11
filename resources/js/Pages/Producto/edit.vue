<script setup lang="ts">
import { reactive, ref, computed, resolveComponent } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import FormField from '../../components/FormField.vue'

const UBadge = resolveComponent('UBadge')

interface Producto {
  id: string
  categoriaId: string
  codigo: string
  nombre: string
  descripcion?: string
  precioUnitario: number
  stock: number
  tipo: string
  activo: boolean
  createdAt: string
  updatedAt: string
}

const props = defineProps<{
  producto: Producto
}>()

const state = reactive({
  categoriaId: props.producto.categoriaId,
  codigo: props.producto.codigo,
  nombre: props.producto.nombre,
  descripcion: props.producto.descripcion || '',
  precioUnitario: props.producto.precioUnitario,
  stock: props.producto.stock,
  tipo: props.producto.tipo,
  activo: props.producto.activo
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

  router.put(route('productos.update', props.producto.id), state, {
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
</script>

<template>
  <UDashboardPanel>
    <template #header>
      <UDashboardNavbar title="Editar Producto">
        <template #leading>
          <UDashboardSidebarCollapse />
        </template>
      </UDashboardNavbar>
    </template>

    <template #body>
      <div class="p-6">
        <div class="mb-6">
          <h2 class="text-2xl font-semibold">Editar Producto</h2>
          <p class="text-sm text-muted mt-1">Modifique los datos del producto</p>
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-6">
          <div class="grid grid-cols-2 gap-8">
            <FormField label="Código" name="codigo" required :error="errors.codigo">
              <UInput
                v-model="state.codigo"
                placeholder="PROD-XXXX"
                icon="i-lucide-hash"
                size="xl"
                class="w-full"
                disabled
              />
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

          <div class="bg-muted/50 rounded-lg p-4 text-sm">
            <p class="text-muted">
              <strong>ID:</strong> {{ producto.id }}
            </p>
            <p class="text-muted mt-1">
              <strong>Creado:</strong> {{ new Date(producto.createdAt).toLocaleString('es-ES') }}
            </p>
            <p class="text-muted mt-1">
              <strong>Actualizado:</strong> {{ new Date(producto.updatedAt).toLocaleString('es-ES') }}
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
              label="Actualizar Producto"
              icon="i-lucide-save"
              :loading="isLoading"
            />
          </div>
        </form>
      </div>
    </template>
  </UDashboardPanel>
</template>
